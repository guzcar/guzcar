<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ---------------------------------------------------------
        // 1. EQUIPO DETALLES 
        // ---------------------------------------------------------
        
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bi_asignar");
        DB::unprepared("
        CREATE TRIGGER trg_ed_bi_asignar BEFORE INSERT ON equipo_detalles FOR EACH ROW
        BEGIN
          IF NEW.implemento_id IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'implemento_id requerido';
          END IF;
          UPDATE implementos
             SET stock = stock - 1, asignadas = asignadas + 1
           WHERE id = NEW.implemento_id AND stock >= 1;
          IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para asignar implemento';
          END IF;
          IF NEW.ultimo_estado IS NULL OR NEW.ultimo_estado <> 'OPERATIVO' THEN
            SET NEW.ultimo_estado = 'OPERATIVO';
          END IF;
          SET NEW.deleted_at = NULL;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bu_cambiar_implemento");
        DB::unprepared("
        CREATE TRIGGER trg_ed_bu_cambiar_implemento BEFORE UPDATE ON equipo_detalles FOR EACH ROW
        BEGIN
          IF NEW.implemento_id <> OLD.implemento_id THEN
            UPDATE implementos
               SET asignadas = asignadas - 1, stock = stock + 1
             WHERE id = OLD.implemento_id AND asignadas >= 1;
            IF ROW_COUNT() = 0 THEN
               SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error consistencia: Asignadas insuficientes';
            END IF;
            UPDATE implementos
               SET stock = stock - 1, asignadas = asignadas + 1
             WHERE id = NEW.implemento_id AND stock >= 1;
            IF ROW_COUNT() = 0 THEN
               SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente en nuevo implemento';
            END IF;
            IF NEW.ultimo_estado IS NULL OR NEW.ultimo_estado <> 'OPERATIVO' THEN
               SET NEW.ultimo_estado = 'OPERATIVO';
            END IF;
            SET NEW.deleted_at = NULL;
          END IF;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bd_liberar");
        DB::unprepared("
        CREATE TRIGGER trg_ed_bd_liberar BEFORE DELETE ON equipo_detalles FOR EACH ROW
        BEGIN
          IF (OLD.ultimo_estado IS NULL OR OLD.ultimo_estado = 'OPERATIVO') THEN
            UPDATE implementos
               SET asignadas = asignadas - 1, stock = stock + 1
             WHERE id = OLD.implemento_id AND asignadas >= 1;
            IF ROW_COUNT() = 0 THEN
               SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al liberar: Asignadas insuficientes';
            END IF;
          END IF;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bu_toggle_activo");
        DB::unprepared("
        CREATE TRIGGER trg_ed_bu_toggle_activo BEFORE UPDATE ON equipo_detalles FOR EACH ROW
        BEGIN
          IF OLD.ultimo_estado = 'OPERATIVO' AND (NEW.ultimo_estado <=> OLD.ultimo_estado) THEN
            IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
              UPDATE implementos
                 SET asignadas = asignadas - 1, stock = stock + 1
               WHERE id = OLD.implemento_id AND asignadas >= 1;
              IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Asignadas insuficientes'; END IF;
            ELSEIF OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL THEN
              UPDATE implementos
                 SET stock = stock - 1, asignadas = asignadas + 1
               WHERE id = OLD.implemento_id AND stock >= 1;
              IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para restaurar'; END IF;
            END IF;
          END IF;
        END
        ");

        // ---------------------------------------------------------
        // 2. CONTROL EQUIPOS 
        // ---------------------------------------------------------

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ce_bi_set_propietario");
        DB::unprepared("
        CREATE TRIGGER trg_ce_bi_set_propietario BEFORE INSERT ON control_equipos FOR EACH ROW
        BEGIN
          DECLARE v_prop BIGINT UNSIGNED;
          IF NEW.propietario_id IS NULL THEN
            SELECT propietario_id INTO v_prop FROM equipos WHERE id = NEW.equipo_id LIMIT 1;
            IF v_prop IS NOT NULL THEN SET NEW.propietario_id = v_prop; END IF;
          END IF;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ce_ai_crear_detalles");
        DB::unprepared("
        CREATE TRIGGER trg_ce_ai_crear_detalles AFTER INSERT ON control_equipos FOR EACH ROW
        BEGIN
          INSERT INTO control_equipo_detalles
            (control_equipo_id, equipo_detalle_id, implemento_id, estado, prev_estado, prev_deleted_at)
          SELECT NEW.id, ed.id, ed.implemento_id, 'OPERATIVO', ed.ultimo_estado, ed.deleted_at
            FROM equipo_detalles ed
           WHERE ed.equipo_id = NEW.equipo_id AND ed.deleted_at IS NULL;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ced_bi_guardasaneidad");
        DB::unprepared("
        CREATE TRIGGER trg_ced_bi_guardasaneidad BEFORE INSERT ON control_equipo_detalles FOR EACH ROW
        BEGIN
          DECLARE v_imp BIGINT UNSIGNED;
          DECLARE v_equipo BIGINT UNSIGNED;
          DECLARE v_equipo_control BIGINT UNSIGNED;
          DECLARE v_prev_estado ENUM('OPERATIVO','MERMA','PERDIDO');
          DECLARE v_prev_deleted_at DATETIME;

          SELECT implemento_id, equipo_id, ultimo_estado, deleted_at
            INTO v_imp, v_equipo, v_prev_estado, v_prev_deleted_at
            FROM equipo_detalles WHERE id = NEW.equipo_detalle_id LIMIT 1;

          IF v_imp IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'equipo_detalle inexistente'; END IF;

          SELECT equipo_id INTO v_equipo_control FROM control_equipos WHERE id = NEW.control_equipo_id LIMIT 1;

          IF v_equipo_control IS NULL OR v_equipo_control <> v_equipo THEN
             SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El detalle no pertenece al equipo del control';
          END IF;

          SET NEW.implemento_id = v_imp;
          IF NEW.prev_estado IS NULL THEN SET NEW.prev_estado = v_prev_estado; END IF;
          IF NEW.prev_deleted_at IS NULL THEN SET NEW.prev_deleted_at = v_prev_deleted_at; END IF;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ced_bu_transiciones");
        DB::unprepared("
        CREATE TRIGGER trg_ced_bu_transiciones BEFORE UPDATE ON control_equipo_detalles FOR EACH ROW
        BEGIN
          DECLARE v_old, v_new ENUM('OPERATIVO','MERMA','PERDIDO');
          DECLARE v_imp BIGINT UNSIGNED;
          
          SET v_old = IFNULL(OLD.estado, 'OPERATIVO');
          SET v_new = IFNULL(NEW.estado, 'OPERATIVO');
          SET v_imp = NEW.implemento_id;

          IF NOT (v_old <=> v_new) THEN
            IF v_old = 'OPERATIVO' AND v_new = 'MERMA' THEN
               UPDATE implementos SET asignadas = asignadas - 1, mermas = mermas + 1 WHERE id = v_imp AND asignadas >= 1;
               UPDATE equipo_detalles SET ultimo_estado = 'MERMA', deleted_at = IFNULL(deleted_at, NOW()) WHERE id = OLD.equipo_detalle_id;
            
            ELSEIF v_old = 'OPERATIVO' AND v_new = 'PERDIDO' THEN
               UPDATE implementos SET asignadas = asignadas - 1, perdidas = perdidas + 1 WHERE id = v_imp AND asignadas >= 1;
               UPDATE equipo_detalles SET ultimo_estado = 'PERDIDO', deleted_at = IFNULL(deleted_at, NOW()) WHERE id = OLD.equipo_detalle_id;
            
            ELSEIF v_old IN ('MERMA','PERDIDO') AND v_new = 'OPERATIVO' THEN
               IF v_old = 'MERMA' THEN UPDATE implementos SET mermas = mermas - 1, asignadas = asignadas + 1 WHERE id = v_imp; END IF;
               IF v_old = 'PERDIDO' THEN UPDATE implementos SET perdidas = perdidas - 1, asignadas = asignadas + 1 WHERE id = v_imp; END IF;
               UPDATE equipo_detalles SET ultimo_estado = 'OPERATIVO', deleted_at = NULL WHERE id = OLD.equipo_detalle_id;
            END IF;
          END IF;
        END
        ");

        // ---------------------------------------------------------
        // 3. ENTRADAS DE STOCK 
        // ---------------------------------------------------------
        
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ied_bi_sumar_stock");
        DB::unprepared("
        CREATE TRIGGER trg_ied_bi_sumar_stock BEFORE INSERT ON implemento_entrada_detalles FOR EACH ROW
        BEGIN
          UPDATE implementos SET stock = stock + NEW.cantidad WHERE id = NEW.implemento_id;
        END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ied_bd_restar_stock");
        DB::unprepared("
        CREATE TRIGGER trg_ied_bd_restar_stock BEFORE DELETE ON implemento_entrada_detalles FOR EACH ROW
        BEGIN
          UPDATE implementos SET stock = stock - OLD.cantidad WHERE id = OLD.implemento_id AND stock >= OLD.cantidad;
          IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para eliminar detalle entrada'; END IF;
        END
        ");

        // ---------------------------------------------------------
        // 4. INCIDENCIAS (CORREGIDO)
        // ---------------------------------------------------------

        DB::unprepared("DROP TRIGGER IF EXISTS trg_ii_bi_aplicar");
        DB::unprepared("
        CREATE TRIGGER trg_ii_bi_aplicar BEFORE INSERT ON implemento_incidencias FOR EACH ROW
        BEGIN
          DECLARE v_imp BIGINT UNSIGNED;
          DECLARE v_equipo BIGINT UNSIGNED;
          DECLARE v_prop BIGINT UNSIGNED;
          DECLARE v_prev_estado_temp ENUM('OPERATIVO','MERMA','PERDIDO'); -- Variable temporal añadida
          
          -- Lógica para Origen EQUIPO
          IF NEW.tipo_origen = 'EQUIPO' THEN
             IF NEW.equipo_detalle_id IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Requerido equipo_detalle_id'; END IF;
             SET NEW.cantidad = 1;
             
             -- CORRECCION: Seleccionar en variable temporal, no en NEW.prev_estado
             SELECT implemento_id, equipo_id, ultimo_estado 
               INTO v_imp, v_equipo, v_prev_estado_temp
               FROM equipo_detalles WHERE id = NEW.equipo_detalle_id;
             
             SET NEW.implemento_id = v_imp;
             SET NEW.prev_estado = v_prev_estado_temp; -- Asignación correcta
             
             IF NEW.propietario_id IS NULL THEN
                SELECT propietario_id INTO v_prop FROM equipos WHERE id = v_equipo;
                SET NEW.propietario_id = v_prop;
             END IF;

             IF NEW.motivo = 'MERMA' THEN
                UPDATE implementos SET asignadas = asignadas - 1, mermas = mermas + 1 WHERE id = v_imp AND asignadas >= 1;
                UPDATE equipo_detalles SET ultimo_estado = 'MERMA', deleted_at = NOW() WHERE id = NEW.equipo_detalle_id;
             ELSEIF NEW.motivo = 'PERDIDO' THEN
                UPDATE implementos SET asignadas = asignadas - 1, perdidas = perdidas + 1 WHERE id = v_imp AND asignadas >= 1;
                UPDATE equipo_detalles SET ultimo_estado = 'PERDIDO', deleted_at = NOW() WHERE id = NEW.equipo_detalle_id;
             END IF;

          -- Lógica para Origen STOCK
          ELSEIF NEW.tipo_origen = 'STOCK' THEN
             IF NEW.motivo = 'MERMA' THEN
                UPDATE implementos SET stock = stock - NEW.cantidad, mermas = mermas + NEW.cantidad WHERE id = NEW.implemento_id AND stock >= NEW.cantidad;
             ELSEIF NEW.motivo = 'PERDIDO' THEN
                UPDATE implementos SET stock = stock - NEW.cantidad, perdidas = perdidas + NEW.cantidad WHERE id = NEW.implemento_id AND stock >= NEW.cantidad;
             END IF;
             IF ROW_COUNT() = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para incidencia'; END IF;
          END IF;
        END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ii_bi_aplicar");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ied_bd_restar_stock");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ied_bi_sumar_stock");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ced_bu_transiciones");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ced_bi_guardasaneidad");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ce_ai_crear_detalles");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ce_bi_set_propietario");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bu_toggle_activo");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bd_liberar");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bu_cambiar_implemento");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_ed_bi_asignar");
    }
};
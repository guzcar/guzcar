<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class RepuestoCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth'; 

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Repuestos';

    protected static ?string $clusterBreadcrumb = 'Repuestos';

    protected static ?int $navigationSort = 80;
}
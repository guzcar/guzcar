<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class EntradaCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Entradas';

    protected static ?string $clusterBreadcrumb = 'Entradas';

    protected static ?int $navigationSort = 50;
}

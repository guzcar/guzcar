<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class SalidaCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Salidas';

    protected static ?string $clusterBreadcrumb = 'Salidas';

    protected static ?int $navigationSort = 40;
}

<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ArticuloCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Logística';

    protected static ?string $navigationLabel = 'Artículos';

    protected static ?string $clusterBreadcrumb = 'Artículos';

    protected static ?int $navigationSort = 70;
}

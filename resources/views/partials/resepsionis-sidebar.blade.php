@php
    $menuItems = [
        [
            'route' => 'resepsionis.dashboard',
            'icon' => 'fluentui-home-24',
            'label' => 'Beranda',
        ],
        [
            'route' => 'resepsionis.riwayat',
            'icon' => 'gmdi-history',
            'label' => 'Riwayat',
        ],
        [
            'route' => 'resepsionis.karyawan',
            'icon' => 'gmdi-people-r',
            'label' => 'Karyawan',
        ],
    ];
@endphp

@foreach($menuItems as $item)
    <a href="{{ route($item['route']) }}" class="sidebar-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
        @svg($item['icon'], 'w-8 h-8')
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
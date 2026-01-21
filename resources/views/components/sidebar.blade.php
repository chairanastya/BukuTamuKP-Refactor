<div class="sidebar {{ $attributes->get('class') }}" id="sidebar" {{ $attributes->except('class') }}>
    {{ $slot }}
</div>

<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        width: 100px;
        background: linear-gradient(#46B8AD 20%, #0C4777 100%);
        z-index: 30;
        display: flex;
        flex-direction: column;
        padding-top: 116px;
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.25rem 1rem;
        color: white;
        text-decoration: none;
        transition: background 0.2s;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .sidebar-item:hover,
    .sidebar-item.active {
        background: #F7B218;
    }

    .sidebar-item svg {
        width: 32px;
        height: 32px;
        margin-bottom: 0.5rem;
    }

    .sidebar-item span {
        text-align: center;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
        }

        .sidebar.open {
            transform: translateX(0);
        }
    }
</style>
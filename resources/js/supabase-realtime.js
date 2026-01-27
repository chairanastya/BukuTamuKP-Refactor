export function initSupabaseRealtime(options = {}) {
    const {
        channelName = 'realtime-channel',
        tableName = 'table',
        configUrl = '/api/supabase-config',
        cdnUrl = 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2',
        onPayload = null
    } = options;

    let channel = null;
    let supabaseClient = null;

    function loadSupabaseScript() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = cdnUrl;
            script.async = true;

            script.onload = () => {
                if (window.supabase) {
                    resolve(window.supabase);
                } else {
                    reject(new Error('Supabase library not loaded'));
                }
            };

            script.onerror = () => {
                reject(new Error(`Failed to load Supabase library from ${cdnUrl}`));
            };

            document.head.appendChild(script);
        });
    }

    async function initializeClient() {
        try {
            const supabaseLib = await loadSupabaseScript();

            const configResponse = await fetch(configUrl);
            const config = await configResponse.json();

            if (!configResponse.ok || config.error) {
                throw new Error(config.error || `Failed to fetch Supabase config: ${configResponse.status}`);
            }

            if (!config.url || !config.key) {
                throw new Error('Invalid Supabase configuration');
            }

            const { createClient } = supabaseLib;
            supabaseClient = createClient(config.url, config.key);

            setupRealtimeChannel();
        } catch (error) {
            console.error('[initSupabaseRealtime] Error initializing:', error);
        }
    }

    function setupRealtimeChannel() {
        if (!supabaseClient) return;

        channel = supabaseClient
            .channel(channelName)
            .on(
                'postgres_changes',
                { event: '*', schema: 'public', table: tableName },
                (payload) => {
                    console.log('[initSupabaseRealtime] Perubahan terdeteksi:', payload.eventType);

                    if (onPayload && typeof onPayload === 'function') {
                        onPayload(payload);
                    }
                }
            )
            .subscribe((status) => {
                if (status === 'SUBSCRIBED') {
                    console.log('[initSupabaseRealtime] Realtime active untuk tabel', tableName);
                } else if (status === 'CHANNEL_ERROR') {
                    console.error('[initSupabaseRealtime] Channel error');
                } else if (status === 'TIMED_OUT') {
                    console.warn('[initSupabaseRealtime] Subscribe timeout');
                }
            });

        window.addEventListener('beforeunload', () => {
            if (channel) {
                channel.unsubscribe();
            }
        });
    }

    function disconnect() {
        if (channel) {
            channel.unsubscribe();
            channel = null;
        }
    }

    initializeClient();

    return {
        disconnect,
        getChannel: () => channel,
        getClient: () => supabaseClient
    };
}

export default initSupabaseRealtime;

<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
window.onload = (event) => {
    const statObject = document.getElementById('bot-status');
    const uptObject = document.getElementById('bot-uptime');

    setInterval(() => {
        if (uptObject || statObject) {
            fetch('https://Zigger.zige.repl.co/status')
                .then(response => {
                    return response.json();
                })
                .then(res => {
                    if (res) {
                        res = JSON.parse(res);
                        if (uptObject) {
                            function sec2human(seconds) {
                                const h = Math.floor(+seconds / 3600);
                                const m = Math.floor(+seconds % 3600 / 60);
                                const s = Math.floor(+seconds % 3600 % 60);
                                const pad = n => n.toString().padStart(2, '0');
                                let resp = m == 0 && h == 0 ? s : pad(s);
                                if (m || h) resp = (h == 0 ? m : pad(m)) + ':' + resp;
                                if (h) resp = h + ':' + resp;
                                return resp;
                            };
                            uptObject.innerHTML = sec2human(Math.floor(res.uptime / 1000));
                        };
                        if (statObject) {
                            if (res.status == 200) {
                                statObject.innerHTML = 'Online';
                            } else {
                                statObject.innerHTML = 'Offline';
                            }
                        }
                    };
                })
        };
    }, 500)
};</script>
<!-- end Simple Custom CSS and JS -->

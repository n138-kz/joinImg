class Discord {
    logger (level='info', content='') {
        let log = {
            date: (new Date).toISOString(),
            level: level,
            data: content,
        };
        let logstorage = sessionStorage.getItem( (btoa(location.href)).slice(0, 16) + '.'+'log' );
        try { logstorage = ( logstorage === "" || logstorage === null || logstorage === undefined ) ? [] : JSON.parse(logstorage); } catch { logstorage = [] };
        logstorage.unshift(log);
        sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'log', JSON.stringify(logstorage) );
        sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'log_latest', JSON.stringify(log) );
    }
    async login (qlist) {
        if ( qlist.error !== '' || qlist.error_description !== '' ) {
            sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'oauth2_token', null );
            sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'users_me', null );

            let dom = [];
            dom[0] = document.createElement('div');
            dom[0].classList.add('error');
            dom[1] = document.createElement('h2');
            dom[1].innerText = `${qlist.error}`;
            dom[0].appendChild(dom[1]);
            dom[1] = document.createElement('div');
            dom[1].innerText = `${qlist.error_description}`;
            dom[0].appendChild(dom[1]);
            dom[1] = document.createElement('div');
            let log = sessionStorage.getItem( (btoa(location.href)).slice(0, 16) + '.'+'log_latest' );
            try { log = ( log === "" || log === null || log === undefined ) ? {date:'',data:'',level:''} : JSON.parse(log); } catch { log = {date:'',data:'',level:''} };
            dom[1].style.fontSize = '9pt';
            dom[1].style.whiteSpace = 'pre-wrap';
            dom[1].innerText = `[${log.date}][${log.level}] ${log.data}`;

            dom[0].appendChild(dom[1]);

            document.body.prepend(dom[0]);
            return qlist;
        }
        
        if ( qlist.code !== '' ) {
            await this.getAccessToken(qlist.code);
        } else if ( qlist.access_token !== '' ) {
            await this.getUserInfo(qlist.access_token);
        }
    }
    async getAccessToken (code) {
        try {
            let url, req, res;
            url = `https://api.n138.jp/sso_discord/server/token.php?&redirect_url=${location.origin}${location.pathname}&code=${code}`;
            req = await fetch(url, {
                cache: 'no-store',
            });
            
            if (await req.status>299||await req.status<200) {
                throw new Error(`HTTP Error: ${await req.statusText}(code:${await req.status}) ${req.url}`, {cause: `HTTP ${await req.status} Error`});
            }
            res = await req.json();
            res.oauth2_token.expires_at = new Date((Date.now()/10**3+res.oauth2_token.expires_in)*10**3);
            console.debug({ url:url, req:req, res:res, });

            sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'oauth2_token', JSON.stringify(res.oauth2_token) );
            this.logger('info', `code:${code} -> token:${res.oauth2_token.access_token}`);
            location.replace( `${location.origin}${location.pathname}?access_token=${res.oauth2_token.access_token}` );
        } catch(e) {
            console.error(e, {name: e.name, message: e.message, stack: e.stack});
            this.logger('error', e.stack);
            location.replace( `${location.origin}${location.pathname}?error=access_denied&error_description=[getAccessToken]+Something+Error+has+occurred.+Please+confirm+the+log` );
        }
    }
    async getUserInfo (token) {
        try {
            let url, req, res;
            url = 'https://discordapp.com/api/users/@me';
            req = await fetch(url, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
                cache: 'no-store',
            });
            
            if (await req.status>299||await req.status<200) {
                throw new Error(`HTTP Error: ${await req.statusText}(code:${await req.status}) ${req.url}`, {cause: `HTTP ${await req.status} Error`});
            }
            res = await req.json();
            console.debug({ url:url, req:req, res:res, });

            sessionStorage.setItem( (btoa(location.href)).slice(0, 16) + '.'+'users_me', JSON.stringify(res) );

            res.name = ( res.global_name === '' || res.global_name === null || res.global_name === undefined ) ? res.username : res.global_name;
            res.avatar = `https://cdn.discordapp.com/avatars/${res.id}/${res.avatar}`;

            Array.from(document.querySelectorAll('img[data-logo="Discord"]')).map((e)=>{
                {
                    let dom = [];
                    dom[0] = document.createElement('span');
                    dom[1] = document.createElement('a');
                    dom[1].href = '#';
                    dom[1].setAttribute('onclick', `javascript:window.open("https://api.n138.jp/sso_discord/server/token_revoke.php?token=${token}","subwin", "left=0,top=0,width=500,height=150");sessionStorage.clear();location.replace(location.origin+location.pathname)`);
                    dom[1].taget = '_blank';
                    dom[1].innerText = '【ログアウト(β)】';
                    dom[0].appendChild(dom[1]);
                    e.parentNode.parentNode.appendChild(dom[0]);
                }
                {
                    let dom = [];
                    dom[0] = document.createElement('span');
                    dom[1] = document.createElement('a');
                    dom[1].href = 'https://discord.com/channels/@me';
                    dom[1].taget = '_blank';
                    dom[2] = document.createElement('img');
                    dom[2].src = res.avatar;
                    dom[2].alt = 'discord avatar';
                    dom[2].style.maxHeight = '1em';
                    dom[1].appendChild(dom[2]);
                    dom[2] = document.createElement('span');
                    dom[2].innerText = res.name;
                    dom[1].appendChild(dom[2]);
                    dom[0].appendChild(dom[1]);
                    e.parentNode.parentNode.after(dom[0]);
                }
            });
            Array.from(document.querySelectorAll(':disabled')).map((e)=>e.disabled=false);
        } catch(e) {
            console.error(e, {name: e.name, message: e.message, stack: e.stack});
            this.logger('error', e.stack);
            location.replace( `${location.origin}${location.pathname}?error=access_denied&error_description=[getUserInfo]+Something+Error+has+occurred.+Please+confirm+the+log` );
        }
    }
}
console.log('Loaded class.Discord.js');

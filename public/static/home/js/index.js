function setVideo(pull){
        if(pull==''){
            return !1;
        }
        var last_len=pull.lastIndexOf(".")+1;
        var len = pull.length;
        var pathf = pull.substring(last_len,len).toLowerCase();
        
        document.getElementById('video').innerHTML ='';
        if(pathf=='flv'){
            new FlvJsPlayer({
                id: 'video',
                url: pull,
                autoplay: true,
                autoplayMuted: true,
                playsinline:true,
                volume:0.2,
                width:'100%',
                height:'100%',
                fitVideoSize: 'auto',
                ignores: ['time','replay']
            });
            return !0;
        }
        
        if(pathf=='hls'){
            new HlsJsPlayer({
                id: 'video',
                url: pull,
                autoplay: true,
                autoplayMuted: true,
                playsinline:true,
                volume:0.2,
                width:'100%',
                height:'100%',
                fitVideoSize: 'auto',
                ignores: ['time','replay']
            });
            return !0;
        }
        
        new Player({
            id: 'video',
            url: pull,
            autoplay: true,
            autoplayMuted: true,
            playsinline:true,
            loop: true,
            volume:0.2,
            width:'100%',
            height:'100%',
            fitVideoSize: 'auto',
            ignores: ['time','replay']
        });
        
        return !0;
    }
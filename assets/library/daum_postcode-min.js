window.daum=window.daum||{},function(t){function e(){for(var t=0,e=p.length;e>t;t++)document.write('<script charset="UTF-8" type="text/javascript" src="'+(i+p[t])+'"></script>');s=2}function n(){for(var t=0,e=p.length;e>t;t++){var n=document.createElement("script");f[p[t]]=!1,n.type="text/javascript",n.charset="utf-8",n.src=i+p[t],n.onload=function(){var e=p[t];return function(){var t=e;f[t]||(f[t]=!0),a()&&c()}}(),n.onreadystatechange=function(){var e=p[t];return function(){/loaded|complete/.test(this.readyState)&&(f[e]||(f[e]=!0),a()&&c())}}(),document.getElementsByTagName("head")[0].appendChild(n)}}function o(t){var e={};return t.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(t,n,o){e[n]=o}),e}function a(){for(var t=0,e=0,n=p.length;n>e;e++)f[p[e]]&&t++;return t===p.length}function c(){for(s=2;r[0];)r.shift()()}t.postcode={};var r=[],s=0,i="https:"==location.protocol?"https:":"http:",u=document.getElementsByTagName("script"),d=u[u.length-1].src;u=null;var p=["//t1.daumcdn.net/cssjs/postcode/1513129253770/171213.js"];if(/\/map_js_init\/postcode.v2(\.dev){0,1}(\.update){0,1}\.js\b/.test(d)){"false"!==o(d).autoload&&(e(),s=2)}var f={};t.postcode.version="171213",t.postcode.load=function(t){if(t&&"function"==typeof t)switch(r.push(t),s){case 0:s=1,n();break;case 2:c()}}}(window.daum);
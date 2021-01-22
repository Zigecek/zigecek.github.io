<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
window.onload = (event) => {
  const statObject = document.getElementById('bot-status');
  const uptObject = document.getElementById('bot-uptime');

    function httpGet(theUrl) {
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.open("GET", theUrl, false); // false for synchronous request
        xmlHttp.send(null);
        return xmlHttp.responseText;
    }
    var request = httpGet('https://reqres.in/api/users/2');
  
  setInterval(() => {
    if (uptObject || statObject){
      var res = httpGet('https://Zigger.zige.repl.co/status');
      
      if (res){
        res = JSON.parse(res);
        if (uptObject){
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
        if (statObject){
          if (res.status == 200){
            statObject.innerHTML = 'Online';
          } else {
            statObject.innerHTML = 'Offline';
          }
        }
      };
    };
  }, 1000)
};</script>
<!-- end Simple Custom CSS and JS -->

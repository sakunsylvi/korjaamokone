(function() {
  if (document.cookie.split(';').some(function(c) { return c.trim().indexOf('admin_logged_in=') === 0; })) {
    var bar = document.createElement('div');
    bar.style.cssText = 'position:fixed;top:0;left:0;right:0;height:20px;background:#111;display:flex;align-items:center;padding:0 12px;z-index:9999;font-size:11px;';
    var link = document.createElement('a');
    link.href = '/admin/';
    link.textContent = 'Hallintapaneeli \u2192';
    link.style.cssText = 'color:#aaa;text-decoration:none;';
    link.onmouseover = function() { this.style.color = '#fff'; };
    link.onmouseout = function() { this.style.color = '#aaa'; };
    bar.appendChild(link);
    document.body.insertBefore(bar, document.body.firstChild);
    document.body.style.paddingTop = '20px';
  }
})();

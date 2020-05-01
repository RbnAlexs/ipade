var getUrl = window.location;
let html = '<!DOCTYPE HTML> <html> <head> <title>Title of the document</title> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> </head><body style="background: #fff"><div style="position: absolute;top: 50%;left: 50%;-moz-transform: translateX(-50%) translateY(-50%);-webkit-transform: translateX(-50%) translateY(-50%);transform: translateX(-50%) translateY(-50%);"><img src="http://localhost/ipade/wp-content/themes/publisher-child/assets/loader.gif"/></div></body></html>'
wp.hooks.addFilter( 
  'editor.PostPreview.interstitialMarkup', 
  'my-plugin/custom-preview-message', 
  function() { 
    return html; 
  } 
);

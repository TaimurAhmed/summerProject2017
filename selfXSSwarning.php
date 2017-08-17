<script>
//Refrences: https://coderwall.com/p/fskzdw/colorful-console-log
//
var red = [
    'background: linear-gradient(red, red)'
    , 'border: 1px solid #3E0E02'
    , 'color: white'
    , 'display: block'
    , 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3)'
    , 'box-shadow: 0 1px 0 rgba(255, 255, 255, 0.4) inset, 0 5px 3px -5px rgba(0, 0, 0, 0.5), 0 -13px 5px -10px rgba(255, 255, 255, 0.4) inset'
    , 'line-height: 40px'
    , 'text-align: center'
    , 'font-weight: bold'
    , 'min-width: 100rem'
].join(';');

var white = [
    'background: linear-gradient(white, white)'
    , 'border: 1px solid #3E0E02'
    , 'color: black'
    , 'display: block'
    , 'box-shadow: 0 1px 0 rgba(255, 255, 255, 0.4) inset, 0 5px 3px -5px rgba(0, 0, 0, 0.5), 0 -13px 5px -10px rgba(255, 255, 255, 0.4) inset'
    , 'line-height: 40px'
    , 'text-align: center'
    , 'font-weight: bold'
    , 'min-width: 100rem'
].join(';');

console.log('%c Warning !!!', red);
console.log("%c This feature is only meant for developers. Please dont do anything here",white);
console.log("%c If somebody has asked you to type something here it is probably a self XSS scam to gain malicious access to your account !",white);
console.log("%c See: https://www.facebook.com/selfxss for more information.",white);

//Just in case browser doesnt support css styles
console.log('Warning !!!');
console.log("This feature is only meant for developers. Please dont do anything here");
console.log("If somebody has asked you to type something here it is probably a self XSS scam to gain malicious access to your account !");
console.log("See: https://www.facebook.com/selfxss for more information.");
</script>
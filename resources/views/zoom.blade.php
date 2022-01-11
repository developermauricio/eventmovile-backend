<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Zoom Meeting</title>

    <!-- import #zmmtg-root css -->
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.8.1/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.8.1/css/react-select.css" />
</head>

<body>
    <!--<button id="join-meet" name="button" style="position:absolute; z-index:1000">Join Meet</button>-->
    <div id="zmmtg-root"></div>
    
    <!-- import ZoomMtg dependencies -->
    <script src="https://source.zoom.us/1.8.1/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/1.8.1/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/1.8.1/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/1.8.1/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/1.8.1/lib/vendor/jquery.min.js"></script>
    <script src="https://source.zoom.us/1.8.1/lib/vendor/lodash.min.js"></script>

    <!-- import ZoomMtg -->
    <script src="https://source.zoom.us/zoom-meeting-1.8.1.min.js"></script>
    <script>
        const API_KEY = "-kLhp71ZSEWMCVWFRKVJRA";
        const API_SECRET = "amBYfmYNL94sdc2sWNqAi8VMMqtw3ADKwsm2";
        const zoomMeeting = document.getElementById("zmmtg-root")

function init(){
    ZoomMtg.setZoomJSLib('https://dmogdx0jrul3u.cloudfront.net/1.8.1/lib', '/av'); 
    ZoomMtg.preLoadWasm();
    ZoomMtg.prepareJssdk();
    //var browseinfo = ZoomMtg.checkSystemRequirements();
    //console.log(browseinfo)
    //zoomMeeting.style.display = "none";
    //var buttonMeet = document.getElementById('join-meet');
    //signature()
}

function MeetConf (meetingId, nickname, leaveURL, passWord){
    return {
    apiKey: API_KEY,
    apiSecret: API_SECRET,
    meetingNumber: meetingId,
    userName: nickname,
    passWord: passWord,
    leaveUrl:leaveURL,
}};

function signature(meetId = 76749145413, username = 'Guest', leaveURL = "https://google.com.co", passWord="dXJ6ajQxTUVxVXM2NWpsamptbXU2UT09"){
    
    console.log("Function config meet")
    const meetConfig = MeetConf(meetId , username, leaveURL, passWord);
    // Generate Signature function
    const signature = ZoomMtg.generateSignature({
        meetingNumber: meetConfig.meetingNumber,
        apiKey: meetConfig.apiKey,
        apiSecret: meetConfig.apiSecret,
        role: 1,
        success: (res) => {
            // eslint-disable-next-line                             
            console.log("Make signature success: " + res.result);
        }
    });
    // join function
    
    this.initZoom(meetConfig,signature, leaveURL)
}

function initZoom(meetConfig, signature, leaveURL){
    zoomMeeting.style.display="block"
    ZoomMtg.init({
    leaveUrl: leaveURL,
    isSupportAV: true,

    success: (success) => {
        console.log('Trying to join ' + JSON.stringify(success));
        ZoomMtg.join({
            signature: signature,
            meetingNumber: meetConfig.meetingNumber,
            userName: meetConfig.userName,
            apiKey: meetConfig.apiKey,
            userEmail: '',
            passWord: meetConfig.passWord,
            success: (res) => {
                // eslint-disable-next-line
                console.log("Join meeting success");
            },
            error: (res) => {
                // eslint-disable-next-line
               
                    console.log(res);
            }
        });
    },
    error: (res) => {
        // eslint-disable-next-line
        console.log(res);
    }
})
}

function bindEvent(element, eventName, eventHandler) {
    if (element.addEventListener) {
        element.addEventListener(eventName, eventHandler, false);
    } else if (element.attachEvent) {
        element.attachEvent('on' + eventName, eventHandler);
    }
}

let message = {}
window.signature  = signature

window.onload = function(){
    console.log("Start ZOOM script")
    init()
    bindEvent(window, 'message', function (e) {
        console.log("Data received from parent")
        message = JSON.parse(e.data)
        signature(Number(message.meetId), message.username, message.leaveURL, message.passWord)
    });
}


    </script>
    <!-- import local .js file -->
</body>

</html>

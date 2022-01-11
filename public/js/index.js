var API_KEY = "mipQVZA4QICj71qO0QsKIA";
var API_SECRET = "96BbgmotDimIBiMefwVOBriXo5lyzSk4mz4l";

function init(){
    ZoomMtg.setZoomJSLib('https://dmogdx0jrul3u.cloudfront.net/1.7.9/lib', '/av'); 
    

    const zoomMeeting = document.getElementById("zmmtg-root")
    var browseinfo = ZoomMtg.checkSystemRequirements();
    console.log(browseinfo)

    zoomMeeting.style.display = "none";

    var buttonMeet = document.getElementById('join-meet');
    buttonMeet.addEventListener('click', signature());
    
}

function MeetConf (meetingId, nickname){
    return {
    apiKey: API_KEY,
    apiSecret: API_SECRET,
    meetingNumber: meetingId,
    userName: nickname,
    passWord: 'dXJ6ajQxTUVxVXM2NWpsamptbXU2UT09',
    leaveUrl: "https://google.com.co",
    role: 0
}};

function signature(){
    ZoomMtg.preLoadWasm();
    ZoomMtg.prepareJssdk();
    
    const meetConfig = MeetConf(76749145413 , 'pepe');
    // Generate Signature function
    const signature = ZoomMtg.generateSignature({
        meetingNumber: meetConfig.meetingNumber,
        apiKey: meetConfig.apiKey,
        apiSecret: meetConfig.apiSecret,
        role: meetConfig.role,
        success: (res) => {
            // eslint-disable-next-line                             
            console.log("success signature: " + res.result);
        }
    });
    // join function
    
    this.initZoom(meetConfig,signature)
}

function initZoom(meetConfig, signature){
    document.getElementById('zmmtg-root').style.display="block"
    ZoomMtg.init({
    leaveUrl: "https://www.google.com.co",
    isSupportAV: true,
    success: (success) => {
        console.log('trying to join ' + JSON.stringify(success));
        ZoomMtg.join({
            signature: signature,
            meetingNumber: meetConfig.meetingNumber,
            userName: meetConfig.userName,
            apiKey: meetConfig.apiKey,
            userEmail: '',
            passWord: meetConfig.passWord,
            success: (res) => {
                // eslint-disable-next-line
                console.log("join meeting success");
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

window.onload = function(){
    init()
}
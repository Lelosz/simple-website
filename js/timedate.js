function showtime()
{
    var now = new Date();
    var day = now.getDate();
    var month = now.getMonth()+1;
    var year = now.getFullYear();

    var hours = now.getHours();
    if(hours<10) hours = "0"+hours;
    
    var minutes = now.getMinutes();
    if(minutes<10) minutes = "0"+minutes;

    var seconds = now.getSeconds();
    if(seconds<10) seconds = "0"+seconds;

    document.getElementById("dzien").innerHTML=
    day+"/"+month+"/"+year;

    document.getElementById("godzina").innerHTML=
    hours+":"+minutes+":"+seconds;
    
    setTimeout("showtime()",1000);
}
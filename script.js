console.log("in");

setTimeout(function(){
    console.log("inside fun");
    
    $("#id").hide();
    console.log("inside fun after");
},10000);
function menuToggle(){		
	if (document.getElementById("navBtn").className == "navOpen") {
		document.getElementById("navBtn").className = "";
		document.getElementById("listMenu").className = "";
		document.getElementById("shadowbox").className = "";
		/*document.getElementById("left-nav").className = "";*/
	} else {
		document.getElementById("navBtn").className = "navOpen";
		document.getElementById("listMenu").className = "listOpen";
		document.getElementById("shadowbox").className = "visible";
		/*document.getElementById("left-nav").className = "open-side-menu";*/
	}
}

function menuOpen(){
	window.scrollTo(0,0);
	document.getElementById("listMenu").className = "listOpen";
	document.getElementById("shadowbox").className = "visible";
	$("#navBtnShow").hide();
	$("body").addClass("stop-scrolling");
	/*document.getElementById("left-nav").className = "open-side-menu";*/
}

function menuClose(){
	document.getElementById("listMenu").className = "";
	document.getElementById("shadowbox").className = "";
	$("#navBtnShow").show();
	$("body").removeClass("stop-scrolling");
	/*document.getElementById("left-nav").className = "";*/
}


var dashboard = {
    init:()=>{
        dashboard.item.includeHTML();
        setTimeout(() => {
            let userData = localStorage.getItem("userInfo");
            
            if(userData != null){
                userData = JSON.parse(atob(userData));
                let userFullname = `${userData.firstname} ${userData.middlename} ${userData.lastname}`;

                $("#user_full_name").text(userFullname)
            }else{
                window.location.href = base_rdr_url + 'web/dashboard/'
            }
        }, 100);
        

        
    },
    item:{
        includeHTML:() => {
            
            var z, i, elmnt, file, xhttp;
            /* Loop through a collection of all HTML elements: */
            z = document.getElementsByTagName("*");
            for (i = 0; i < z.length; i++) {
              elmnt = z[i];
              /*search for elements with a certain atrribute:*/
              file = elmnt.getAttribute("w3-include-html");

              if (file) {
                /* Make an HTTP request using the attribute value as the file name: */
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                  if (this.readyState == 4) {
                    if (this.status == 200) {elmnt.innerHTML = this.responseText;}
                    if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
                    /* Remove the attribute, and call this function once more: */
                    elmnt.removeAttribute("w3-include-html");
                    dashboard.item.includeHTML();
                  }
                }
                xhttp.open("GET", file, true);
                xhttp.send();
                /* Exit the function: */
                return;
              }
            }
        },
    },
}



dashboard.init();

$("#logout").click(function(){
  alert("im here")
})


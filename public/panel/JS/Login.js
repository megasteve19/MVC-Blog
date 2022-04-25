function LogMe()
{
    this.setAttribute("disabled", true);
    let Username = document.getElementById("Username").value;
    let Password = document.getElementById("Password").value;
    let Message = document.querySelector(".Message");

    Message.innerText = "Bi' saniye bekletiyorum...";
    Message.classList.add("Info");
    Message.classList.remove("Hide");

    $.post("?action=log", {Username: Username, Password: Password}, function(Response)
    {
        document.getElementById("Log").removeAttribute("disabled");
        Response = (Response == "true");

        if(Response)
        {
            Message.classList.remove("Info");
            Message.classList.remove("Danger");
            Message.classList.add("Success");
            Message.innerText = "Giriş yapıldı! Yönlendiriyorum..."
            window.location.href = "./";
        }
        else
        {
            Message.classList.remove("Info");
            Message.classList.add("Danger");
            Message.innerText = "Giriş başarısız!";
        }
    });
}

$(document).ready(function()
{
    document.getElementById("Log").addEventListener("click", LogMe);
});
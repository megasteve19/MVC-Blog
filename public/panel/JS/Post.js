Panel.Post =
{
    SendAs: undefined,
    Send: undefined,
    Delete: undefined,
    Id: undefined,
    Get:
    {
        All: undefined,
        Editable: undefined
    },
    RenderPosts: undefined
};

Panel.Post.Send = function(Callback)
{
    Panel.Editor.GetData().then(function(Post)
    {
        if(Panel.Post.SendAs == "new")
        {
            console.log(Post);

            $.post("?action=add", Post, function(data)
            {
                console.log(data);

                Callback();
            });
        }
        else
        {
            Post.Id = Panel.Post.Id;
            $.post("?action=update", Post, function(data)
            {
                console.log(data);
                Callback();
            });
        }
    });
};

Panel.Post.Delete = async function(Id)
{
    $.post("?action=delete", {Id: Id}, function(data)
    {
        Panel.Post.Get.All(Panel.Post.RenderPosts);
    });
};

Panel.Post.Get.All = function(Callback)
{
    $.get("?action=get", function(Response)
    {
        Response = JSON.parse(Response);
        Callback(Response);
    });
}

Panel.Post.Get.Editable = function(Id, Callback)
{
    $.get("?action=geteditable", {id: Id}, function(Response)
    {
        Response = JSON.parse(Response);
        Callback(Response);
    });
}

Panel.Post.RenderPosts = function(Posts)
{
    let CardGrid = document.querySelector(".Card-Grid");
    CardGrid.innerHTML = "";
    let Template = document.getElementById("CardTemplate");
    Posts.forEach(function(Post)
    {
        let Card = Template.content.cloneNode(true);
        if(Post.Thumbnail != undefined)
        {
            Card.querySelector(".Head img").setAttribute("src", Post.Thumbnail + "?t=" + new Date().getTime());
        }
        else
        {
            Card.querySelector(".Head img").setAttribute("src", "http://mvcblog.com/Images/Posts/empty.jpeg");
        }
        Card.querySelector(".Head h4").innerText = Post.Date;
        Card.querySelector(".Head h1").innerText = Post.Title;
        Card.querySelector(".Body p").innerText = Post.Content;

        Card.querySelector("button.Delete").addEventListener("click", function()
        {
            Panel.Dialog.Pop("Gönderi silinecek", `'${Post.Title}' isimli gönderi silinecek, emin misiniz?`, function()
            {
                Panel.Loader.Open();
                Panel.Post.Delete(Post.Id);
            });
        });

        Card.querySelector("button.Edit").addEventListener("click", function()
        {
            Panel.Loader.Open();
            Panel.Post.Get.Editable(Post.Id, function(Post)
            {
                Panel.Editor.SetData(Post).then(function()
                {
                    Panel.Post.Id = Post.Id;
                    Panel.Post.SendAs = "update";
                    Panel.Editor.Open();
                    setTimeout(Panel.Loader.Close, 300);
                });
            });
        });

        CardGrid.appendChild(Card);
    });

    Panel.Loader.Close();
};
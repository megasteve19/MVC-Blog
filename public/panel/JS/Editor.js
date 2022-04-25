Panel.Editor =
{
    CleanEditor: undefined,
    GetData: undefined,
    SetData: undefined,
    Deploy: undefined,
    Open: undefined,
    Close: undefined
};

Panel.Editor.CleanEditor = function()
{
    //Get elements
    let EditorThumbnail = document.getElementById("EditorThumbnail");
    let ImageFile = document.getElementById("ImageFile");
    let Base64Thumbnail = document.getElementById("Base64Thumbnail");
    let Title = document.getElementById("Title");
    let Content = document.getElementById("Content");

    //Clean em up!
    EditorThumbnail.removeAttribute("src");
    ImageFile.value = "";
    Base64Thumbnail.value = "";
    Title.value = "";
    Content.firstChild.innerHTML = "";
};

Panel.Editor.GetData = async function()
{
    let ImageFile = document.getElementById("ImageFile").files[0];
    let Base64Thumbnail = document.getElementById("Base64Thumbnail");
    let Title = document.getElementById("Title").value;
    let Content = document.getElementById("Content").firstChild.innerHTML;

    let Thumbnail;
    let Accept = ["image/jpeg", "image/png"]
    if(ImageFile != undefined && Accept.includes(ImageFile.type))
    {
        let ResolveImage = new Promise(function(Resolve)
        {
            let Reader = new FileReader();
            Reader.onload = function()
            {
                Resolve(Reader.result);
            };
            Reader.readAsDataURL(ImageFile);
        });

        Thumbnail = await ResolveImage;
    }
    else if(Base64Thumbnail != "")
    {
        Thumbnail = Base64Thumbnail.value;
    }

    if(Thumbnail == undefined || Thumbnail == "")
    {
        Thumbnail = "";
    }

    let Post =
    {
        Thumbnail: Thumbnail,
        Title: Title,
        Content: Content
    };

    return Post;
};

Panel.Editor.SetData = async function(Post)
{
    let EditorThumbnail = document.getElementById("EditorThumbnail");
    let Base64Thumbnail = document.getElementById("Base64Thumbnail");
    let Title = document.getElementById("Title");
    let Content = document.getElementById("Content");

    if(Post.Thumbnail != "")
    {
        EditorThumbnail.setAttribute("src", Post.Thumbnail);
        Base64Thumbnail.value = Post.Thumbnail;
    }
    else
    {
        EditorThumbnail.setAttribute("src" , "");
        Base64Thumbnail.value = "";
    }

    Title.value = Post.Title;
    Content.firstChild.innerHTML = Post.Content;
}

Panel.Editor.Deploy = function()
{
    let QuillConfig =
    {
        theme: "snow",
        modules:
        {
            toolbar:
            [
                [{ 'header': [1, 2, false] }],
                ["bold", "italic", "underline"],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                //[{"align":[]}],
                ["link", "image"]
            ]
        }
    };

    new Quill("#Content", QuillConfig);
}

Panel.Editor.Open = function()
{
    document.body.classList.add("Lock");
    document.getElementById("Editor").classList.add("Active");
}

Panel.Editor.Close = function()
{
    document.body.classList.remove("Lock");
    document.getElementById("Editor").classList.remove("Active");
}
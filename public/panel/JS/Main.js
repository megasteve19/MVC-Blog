$(document).ready(function()
{
    Panel.Editor.Deploy();

    document.getElementById("ImageFile").addEventListener("change", function()
    {
        Panel.Loader.Open();
        Panel.Editor.GetData().then((Post)=>{Panel.Editor.SetData(Post).then(Panel.Loader.Close)});
    });

    document.getElementById("EditorCancel").addEventListener("click", function()
    {
        Panel.Editor.Close();
        Panel.Editor.CleanEditor();
    });

    document.getElementById("DonePost").addEventListener("click", function()
    {
        Panel.Post.Send(function()
        {
            Panel.Post.Get.All(Panel.Post.RenderPosts);
        });
        Panel.Editor.Close();
        Panel.Editor.CleanEditor();
        Panel.Loader.Open();
    });

    document.getElementById("AddPost").addEventListener("click", function()
    {
        Panel.Post.SendAs = "new";
        Panel.Editor.Open();
    });

    Panel.Post.Get.All(Panel.Post.RenderPosts);
});
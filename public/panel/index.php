<?php
    //Config
    $Config = require "../../Config/Config.php";
    $IncPath = $Config["Path"]["RootDir"] . "Source/";

    //Include
    require $IncPath . "Helpers/Helpers.php";
    require $IncPath . "Helpers/Minifiers.php";

    session_start();

    if(empty($_SESSION["LoggedIn"]))
    {
        $_SESSION["LoggedIn"] = false;
    }
    

    //Input
    $Action = (empty($_GET["action"])) ? "index" : $_GET["action"];

    //Include View
    require $IncPath . "View/View.php";

    //Include Models
    require $IncPath . "Model/Post.php";
    require $IncPath . "Model/Admin.php";

    //Include Controllers
    require $IncPath . "Controller/Post.php";

    use MVCBlog\Model as Model;
    use MVCBlog\Controller as Controller;
    use MVCBlog\View as View;

    $PostController = new Controller\Post($Config);
    $View = new View\View($Config["Path"]["RootDir"] . $Config["Path"]["ViewsSubDir"]);
    $View->MinifyOutput = false;

    $UI = ["index", "login"];

    if(in_array($Action, $UI))
    {
        if(!$_SESSION["LoggedIn"] && $Action != "login")
        {
            header("location:http://panel.mvcblog.com/?action=login");
        }

        switch($Action)
        {
            case "index":
                $HeadRender =
                [
                    ["panel/Head/Index"]
                ];
                $BodyRender =
                [
                    ["panel/Body/Loader"],
                    ["panel/Body/Header"],
                    ["panel/Body/DialogTemplate"],
                    ["panel/Body/Post/CardGrid"],
                    ["panel/Body/Post/Editor"],
                    ["panel/Body/Post/CardTemplate"]
                ];
    
                $View->Frame = "panel/Frames/Index";
                break;
            case "login":
                if($_SESSION["LoggedIn"])
                {
                    header("Location: /");
                }
                $HeadRender =
                [
                    ["panel/Head/Login"]
                ];
                $BodyRender =
                [
                    ["panel/Body/Login"]
                ];

                $View->Frame = "panel/Frames/Basic";
                break;
        }

        $View->RenderIn = "head";
        $View->MultipleRender($HeadRender);
    
        $View->RenderIn = "body";
        $View->MultipleRender($BodyRender);
    }
    else
    {
        switch($Action)
        {
            case "get":
                    $PostController->PostPerPage = 1000;
                    $PostController->CurrentPage = 1;
                    $Posts = $PostController->GetPosts();
                    header("Content-Type: text/plain; charset=utf-8");
                    foreach ($Posts as $Key => $Value)
                    {
                        $Posts[$Key]["Date"] = FormatDate($Value["Date"]);
                        $Posts[$Key]["Content"] = TruncateString(strip_tags($Value["Content"]), 512);
                    }
                    $View->RenderText(json_encode($Posts, JSON_PRETTY_PRINT));
                break;
            case "geteditable":
                $Post = $PostController->GetEditablePost($_GET["id"]);

                $View->RenderText(json_encode($Post, JSON_PRETTY_PRINT));
                break;
            case "delete":
                $Id = $_POST["Id"];
                $PostController->DeletePost($Id);
                $View->RenderText("");
                break;
            case "add":
                $Post =
                [
                    "Thumbnail"=>$_POST["Thumbnail"],
                    "Title"=>$_POST["Title"],
                    "Content"=>$_POST["Content"]
                ];

                $PostController->AddPost($Post);
                $View->RenderText("");
                break;
            case "update":
                $Post =
                [
                    "Thumbnail"=>$_POST["Thumbnail"],
                    "Title"=>$_POST["Title"],
                    "Content"=>$_POST["Content"]
                ];

                $PostController->UpdatePost( $_POST["Id"], $Post);
                $View->RenderText("");
                break;
            case "log":
                $AdminModel = new Model\Admin($Config["Path"]["RootDir"]);
                $Username = $AdminModel->FetchPublicInfo()["Username"];
                $Password = $AdminModel->FetchPassword();
                if($_POST["Username"] == $Username && password_verify($_POST["Password"], $Password))
                {
                    $_SESSION["LoggedIn"] = true;
                    $View->RenderText("true");
                }
                else
                {
                    $View->RenderText("false");
                }
                break;
            case "logout":
                session_destroy();
                header("Location: /?action=login");
                break;
        }
    }
?>
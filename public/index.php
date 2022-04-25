<?php
    //Config
    $Config = require "../Config/Config.php";
    $IncPath = $Config["PathInfo"]["Private"]["RootDirectory"] . "Source/";

    //Include
    require $IncPath . "Helpers/Helpers.php";
    require $IncPath . "Helpers/Minifiers.php";
    

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
    $View = new View\View($Config["PathInfo"]["Private"]["RootDirectory"]);

    $HeadRender =
    [
        ["Core/Head/Meta", "Basic Blog|Home"],
        ["Core/Head/Source"]
    ];
    $View->RenderIn = "head";
    $View->MultipleRender($HeadRender);

    switch($Action)
    {
        case 'index':
            $PostController->PostPerPage = 3;
            $PostController->CurrentPage = 1;
            $Posts = $PostController->GetPosts();

            $BodyRender =
            [
                ["Core/Body/Header"],
                ["Other/AboutMe"],
                ["Post/Posts", $Posts],
                ["Core/Body/Footer"]
            ];
            break;
        case 'posts':
            $PostController->PostPerPage = 9;
            $PostController->CurrentPage = (!empty($_GET["page"])) ? $_GET["page"] : 1;
            $Posts = $PostController->GetPosts();
            $Pagination = $PostController->CalculatePagination(2);

            $BodyRender =
            [
                ["Core/Body/Header"],
                ["Post/Posts", $Posts],
                ["Post/Pagination", $Pagination],
                ["Core/Body/Footer"]
            ];
            break;
        case 'post':
            $Id = (!empty($_GET["id"])) ? $_GET["id"] : null;
            $Post = $PostController->GetPost($Id);
            
            $BodyRender =
            [
                ["Core/Body/Header"],
                ["Post/Article", $Post],
                ["Core/Body/Footer"]
            ];
            break;
    }

    $View->RenderIn = "body";
    $View->MultipleRender($BodyRender);
?>
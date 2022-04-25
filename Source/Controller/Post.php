<?php
namespace MVCBlog\Controller
{

    use MVCBlog\Model as Model;

    /**
     * Class for controlling posts.
     * 
     * @since 1.0.0
     */
    class Post
    {
        /**
         * @var int $PostId Requested post by user.
         * 
         * @since 1.0.0
         */
        public int $PostId;

        /**
         * @var int $PostPerPage Max post number of every page.
         * 
         * @since 1.0.0
         */
        public int $PostPerPage;

        private string $ImagesDirectory;

        private string $ImagesPublicDirectory;

        /**
         * @var int $CurrentPage Requested page by user.
         * @see $PostPerPage
         * 
         * @since 1.0.0
         */
        private int $CurrentPage;

        /**
         * @var array $Config App config.
         * 
         * @since 1.0.0
         */
        private array $Config;

        /**
         * @var Model\Post $PostModel Post model for managing database.
         * 
         * @since 1.0.0
         */
        private Model\Post $PostModel;

        /**
         * Post controller init.
         * 
         * @param array $Config App config.
         * @return void
         * 
         * @since 1.0.0
         */
        public function __construct(array $Config)
        {
            $this->PostModel = new Model\Post($Config["DB"]);
            $this->Config = $Config;

            //We don't wanna write this in everywhere right?
            $this->ImagesDirectory = $Config["Path"]["RootDir"] . $Config["Path"]["ImagesSubDir"];
            $this->ImagesPublicDirectory = $Config["Path"]["ImagesSubDomain"];
        }

        public function __set($Variable, $Value)
        {
            switch($Variable)
            {
                case 'CurrentPage':
                    $MaxPage = ceil($this->PostModel->PostCount() / $this->PostPerPage);
                    //Basically just checks page valid or not.
                    if($Value <= 0)
                    {
                        $this->CurrentPage = 1;
                    }
                    else if($Value > $MaxPage)
                    {
                        $this->CurrentPage = $MaxPage;
                    }
                    else
                    {
                        $this->CurrentPage = $Value;
                    }
                    break;
            }
        }

        /**
         * Get posts from database by given page and posts per page.
         * 
         * @return array Posts
         * 
         * @since 1.0.0
         */
        public function GetPosts()
        {
            $Posts = $this->PostModel->FetchPostsByPage($this->CurrentPage, $this->PostPerPage);

            //If thumbnails exist add them into array.
            foreach($Posts as $Key => $Row)
            {
                if(file_exists($this->ImagesDirectory . "$Row[Id].jpeg"))
                {
                    $Posts[$Key]["Thumbnail"] = $this->ImagesPublicDirectory . "$Row[Id].jpeg";
                }
            }

            return $Posts;
        }

        /**
         * Gets post from database by given id.
         * 
         * @return array Post
         * 
         * @since 1.0.0
         */
        public function GetPost(int $Id)
        {
            $Post = $this->PostModel->FetchPostById($Id);

            //If thumbnail exist add into array.
            if(file_exists($this->ImagesDirectory . "$Post[Id].jpeg"))
            {
                $Post["Thumbnail"] = $this->ImagesPublicDirectory . "$Post[Id].jpeg";
            }

            return $Post;
        }

        public function GetEditablePost(int $Id)
        {
            $Post = $this->GetPost($Id);
            $Content = ExtractDOM($Post["Content"]);

            foreach($Content->getElementsByTagName("img") as $Node)
            {
                $ImageName = pathinfo($Node->getAttribute("src"), PATHINFO_FILENAME);
                $Node->setAttribute("src", "data:image/jpeg;base64," . ImageToBase64($this->ImagesDirectory . $ImageName));
            }

            if(!empty($Post["Thumbnail"]))
            {
                $Post["Thumbnail"] = "data:image/jpeg;base64," . ImageToBase64($this->ImagesDirectory . $Id);
            }

            $Post["Content"] = str_replace("\n", "", $Content->saveHTML());

            return $Post;
        }

        /**
         * Adds new post.
         * 
         * @param array $Post Post to add. 
         * @return void
         * 
         * @since 1.0.0
         */
        public function AddPost(array $Post)
        {
            $Title = DoubleQuote($Post["Title"]);
            $Content = ExtractDOM($Post["Content"]);

            //Upload images than assign them to nodes.
            foreach($Content->getElementsByTagName("img") as $Key => $Node)
            {
                $Image = ResizeImage(Base64ToGDImage($Node->getAttribute("src")), 1024);
                $ImageName = UploadImage($Image, 50, $this->ImagesDirectory);

                $Node->setAttribute("src", $this->ImagesPublicDirectory . "$ImageName.jpeg");
            }

            $Content = DoubleQuote(html_entity_decode($Content->saveHTML()));

            //Insert than get id.
            $Id = $this->PostModel->InsertPost(["Title"=>$Title, "Content"=>$Content]);

            if(!empty($Post["Thumbnail"]))
            {
                $Thumbnail = ResizeImage(Base64ToGDImage($Post["Thumbnail"]), 1024);
                UploadThumbnail($Thumbnail, $this->ImagesDirectory . "$Id.jpeg");
            }
        }

        /**
         * Updates post.
         * 
         * @param int $Id Post id to update.
         * @param array $Post New post to replace old one.
         * @return void
         * 
         * @since 1.0.0
         */
        public function UpdatePost(int $Id, array $Post)
        {
            //First fetch post for getting image names.
            $OldPost = $this->PostModel->FetchPostById($Id);
            $OldContent = ExtractDOM($OldPost["Content"]);
            //Than delete every old image.
            foreach ($OldContent->getElementsByTagName("img") as $Node)
            {
                $ImageName = pathinfo($Node->getAttribute("src"), PATHINFO_FILENAME);
                DeleteImage($ImageName, $this->ImagesDirectory);
            }
            //If thumbnail exist delete it too.
            if(file_exists($this->ImagesDirectory . "$OldPost[Id].jpeg"))
            {
                DeleteImage($OldPost["Id"], $this->ImagesDirectory);
            }

            //After that point it's too similar to adding post. Actually i copied the code. Probably i will add another two functions to upload and delete images.
            $Title = DoubleQuote($Post["Title"]);
            $Content = ExtractDOM($Post["Content"]);

            //Upload images than assign them to nodes.
            foreach($Content->getElementsByTagName("img") as $Key => $Node)
            {
                $Image = ResizeImage(Base64ToGDImage($Node->getAttribute("src")), 1024);
                $ImageName = UploadImage($Image, 50, $this->ImagesDirectory);

                $Node->setAttribute("src", $this->ImagesPublicDirectory . "$ImageName.jpeg");
            }
            $Content = DoubleQuote(str_replace("\n", "", html_entity_decode($Content->saveHTML())));
            if(!empty($Post["Thumbnail"]))
            {
                $Thumbnail = ResizeImage(Base64ToGDImage($Post["Thumbnail"]), 1024);
                UploadThumbnail($Thumbnail, $this->ImagesDirectory . "$Id.jpeg");
            }

            $this->PostModel->UpdatePost($Id, ["Title"=>$Title, "Content"=>$Content]);
        }

        /**
         * Deletes a post.
         * 
         * @param int $Id Post id to delete.
         * @return void
         * 
         * @since 1.0.0
         */
        public function DeletePost(int $Id)
        {
            //This function too similar to updating. Stealing chain; AddPost->UpdatePost->DeletePost.

            //First fetch post for getting image names.
            $Post = $this->PostModel->FetchPostById($Id);
            $Content = ExtractDOM($Post["Content"]);
            //Than delete every old image.
            foreach ($Content->getElementsByTagName("img") as $Node)
            {
                $ImageName = pathinfo($Node->getAttribute("src"), PATHINFO_FILENAME);
                DeleteImage($ImageName, $this->ImagesDirectory);
            }
            //If thumbnail exist delete it too.
            if(file_exists($this->ImagesDirectory . "$Post[Id].jpeg"))
            {
                DeleteImage($Post["Id"], $this->ImagesDirectory);
            }

            //Goodbye my little post...
            $this->PostModel->DeletePost($Id);
        }

        /**
         * Calculates pagination by given page and posts per page.
         * 
         * @param int $Offset
         * @return array Valid pages.
         * 
         * @since 1.0.0
         */
        public function CalculatePagination(int $Offset)
        {
            $MaxPages = ceil($this->PostModel->PostCount() / $this->PostPerPage);
            $CurrentPage = $this->CurrentPage;

            $ValidPages =
            [
                "Pages"=>[],
                "CurrentPage"=>$CurrentPage
            ];
            # Actual calculation start. #
            //Start
            if(($CurrentPage - $Offset) <= 0) //If page - offset greater than zero.
            {
                $StartAnchor = 1;
            }
            elseif(($CurrentPage + $Offset) >= $MaxPages) //If page + offset greater than max pages.
            {
                $StartAnchor = $MaxPages - ($Offset * 2);
            }
            else
            {
                $StartAnchor = $CurrentPage -  $Offset;
            }

            //End
            if($StartAnchor == $MaxPages)
            {
                $EndAnchor = $StartAnchor;
            }

            $EndAnchor = $StartAnchor + ($Offset * 2);
            # Actual calculation end. #

            for($i=$StartAnchor; $i <= $EndAnchor; $i++)
            {
                if($i <= $MaxPages)
                {
                    if($i > 0)
                    {
                        array_push($ValidPages["Pages"], $i);
                    }
                }
                else
                {
                    break;
                }
            }

            return $ValidPages;
        }
    }
}
?>

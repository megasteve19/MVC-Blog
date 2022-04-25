<?php
namespace MVCBlog\Model
{

use mysqli;

/**
     * Class for select, insert, update and delete the posts.
     * 
     * @since 1.0.0
     */
    class Post
    {

        /**
         * @var array $DatabaseConfig Database config to connect.
         * 
         * @since 1.0.0
         */
        private array $DatabaseConfig;

        /**
         * Post model init.
         * 
         * @param array $DatabaseConfig Database config to connect.
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function __construct(array $DatabaseConfig)
        {
            $this->DatabaseConfig = $DatabaseConfig;
        }

        ## Get functions start. ##

        /**
         * Fetches posts by page.
         * 
         * @param int $Page Requested page of posts.
         * @param int $PostPerPage Post count per page.
         * @return array Fetched posts.
         * 
         * @since 1.0.0
         */
        public function FetchPostsByPage(int $Page, int $PostPerPage)
        {
            $From = ($Page * $PostPerPage) - $PostPerPage;
            $To = $PostPerPage;

            //Init
            $Connection = $this->GetConnection();

            //Fetch
            $Result = $Connection->query("SELECT * FROM POSTS ORDER BY Id DESC LIMIT $From, $To")->fetch_all(MYSQLI_ASSOC);
            $Connection->close();

            return $Result;
        }

        /**
         * Fetches post by id.
         * 
         * @param int $Id Identifier of the post.
         * @return array Fetched post.
         * 
         * @since 1.0.0
         */
        public function FetchPostById(int $Id)
        {
            //Init
            $Connection = $this->GetConnection();

            //Fetch
            $Result = $Connection->query("SELECT * FROM POSTS WHERE Id = $Id")->fetch_all(MYSQLI_ASSOC)[0];
            $Connection->close();

            return $Result;
        }

        ## Get functions end. ##



        ## Insert functions start. ##

        /**
         * Inserts new post to the database.
         * 
         * @param array $Post Post to insert.
         * @return int Inserted post id.
         * 
         * @since 1.0.0
         */
        public function InsertPost(array $Post)
        {
            //Init
            $Connection = $this->GetConnection();

            //Set
            $Title = $Post["Title"];
            $Content = $Post["Content"];

            //Insert
            $Connection->query("INSERT INTO POSTS(Title, Content) VALUES('$Title', '$Content')");
            $Id = (int)$Connection->insert_id;
            $Connection->close();

            return $Id;
        }

        ## Insert functions end. ##



        ## Update functions start. ##

        /**
         * Updates post in the database.
         * 
         * @param int $Id Post id to update.
         * @param array $Post New values of the post.
         * @retun void
         * 
         * @since 1.0.0
         */
        public function UpdatePost(int $Id, array $Post)
        {
            //Init
            $Connection = $this->GetConnection();

            //Set
            $Title = DoubleQuote($Post["Title"]);
            $Content = DoubleQuote($Post["Content"]);

            //Update
            $Connection->query("UPDATE POSTS SET Title = '$Title', Content = '$Content' WHERE Id = $Id");
            $Connection->close();

            return;
        }

        ## Update functions end. ##



        ## Delete functions start. ##

        /**
         * Deletes post in the database.
         * 
         * @param int $Id Post id to delete.
         * @return void
         * 
         * @since 1.0.0
         */
        public function DeletePost(int $Id)
        {
            //Init
            $Connection = $this->GetConnection();

            //Delete
            $Connection->query("DELETE FROM POSTS WHERE Id = $Id");
            $Connection->close();

            return;
        }

        ## Delete functions end. ##



        ## Other functions start. ##

        /**
         * Initialises a new mysqli connection.
         * 
         * @return mysqli
         * 
         * @since 1.0.0
         */
        private function GetConnection()
        {
            $Connection = new mysqli("localhost", $this->DatabaseConfig["Username"], $this->DatabaseConfig["Password"], $this->DatabaseConfig["Database"]);

            if($Connection->connect_errno)
            {
                die("MySQL connection failed.");
            }
            else
            {
                return $Connection;
            }
        }

        /**
         * Count of posts in the database;
         * 
         * @return int Post count.
         * 
         * @since 1.0.0
         */
        public function PostCount()
        {
            $Connection = $this->GetConnection();
            $Count = $Connection->query("SELECT COUNT(Id) FROM POSTS")->fetch_all(MYSQLI_NUM);
            $Connection->close();

            return  (int)$Count[0][0];
        }

        ## Other functions end. ##
    }
}
?>
<?php
namespace MVCBlog\Model
{
    /**
     * Class for managing admin data.
     * 
     * @since 1.0.0
     */
    class Admin
    {
        /**
         * @var string $PathInfo Absolute path info from config.
         * 
         * @since 1.0.0
         */
        private string $AdminFile;

        /**
         * @param string $RootDirectory Root path of project.
         * @return void
         * 
         * @since 1.0.0
         */
        public function __construct($RootDirectory)
        {
            $this->AdminFile = $RootDirectory . "Database/Admin.json";
        }

        /**
         * Fetches admin public data.
         * 
         * @return array Admin data
         * 
         * @since 1.0.0
         */
        public function FetchPublicInfo()
        {
            //Init
            $File = fopen($this->AdminFile, "r");
            flock($File, LOCK_SH);

            //Get
            $Admin = json_decode(fread($File, filesize($this->AdminFile)), true);

            //Release
            flock($File, LOCK_UN);
            fclose($File);

            return
            [
                "Username"=>$Admin["Username"],
                "FirstName"=>$Admin["FirstName"],
                "LastName"=>$Admin["LastName"],
                "Email"=>$Admin["Email"]
            ];
        }
        
        /**
         * Fetches admin password.
         * 
         * @return string Encrypted password.
         * 
         * @since 1.0.0
         */
        public function FetchPassword()
        {
            //Init
            $File = fopen($this->AdminFile, "r");
            flock($File, LOCK_SH);

            //Get
            $Admin = json_decode(fread($File, filesize($this->AdminFile)), true);

            //Release
            flock($File, LOCK_UN);
            fclose($File);

            return $Admin["Password"];
        }

        /**
         * Updates admin password
         * 
         * @param string $Password New encrypted password.
         * @return void
         * 
         * @since 1.0.0
         */
        public function UpdatePassword(string $Password)
        {
            //Get
            $File = fopen($this->AdminFile, "r");
            flock($File, LOCK_SH);

            $Admin = json_decode(fread($File, filesize($this->AdminFile)), true);

            //Release
            flock($File, LOCK_UN);
            fclose($File);

            //Set
            $File = fopen($this->AdminFile, "w");
            flock($File, LOCK_EX);

            $Admin["Password"] = $Password;

            fwrite($File, json_encode($Admin));

            flock($File, LOCK_UN);
            fclose($File);
        }
    }
}
?>
<?php
namespace MVCBlog\View
{

    use DOMDocument;
    use tidy;

    /**
     * Class for rendering views.
     * 
     * @since 1.0.0
     */
    class View
    {
        /**
         * @var string $RenderIn Specifies where to render. Avaible options is 'head' or 'body'.
         * 
         * @since 1.0.0
         */
        public string $RenderIn;

        /**
         * @var bool $MinifyOutput Specifies minifying output. By the default it's true.
         * 
         * @since 1.0.0
         */
        public bool $MinifyOutput = true;

        /**
         * @var string $Frame Specifies main frame of HTML document.
         * 
         * @since 1.0.0
         */
        public string $Frame;

        /**
         * @var string $ViewDirectory Directory of the views.
         * 
         * @since 1.0.0
         */
        private string $ViewsDirectory;

        /**
         * @var string $HeadContent Head content of the document.
         * 
         * @since 1.0.0
         */
        private string $HeadContent = "";

        /**
         * @var string $BodyContent Body content of the document.
         * 
         * @since 1.0.0
         */
        private string $BodyContent = "";

        /**
         * @var bool $RenderFrame Specifies rendering main frame of HTML. Default is true, until rendering plain text.
         * 
         * @since 1.0.0
         */
        private bool $RenderFrame = true;

        /**
         * View init.
         * 
         * @param string $RootDirectory Root directory of the project.
         * @return void
         * 
         * @since 1.0.0
         */
        public function __construct(string $ViewsDir)
        {
            $this->ViewsDirectory = $ViewsDir;
        }

        /**
         * Renders single view.
         * 
         * @param string $View File path to render view.
         * @param mixed $Variables [Optional] Variable(s) to render with view.
         * @return void
         * 
         * @since 1.0.0
         */
        public function Render(string $View, mixed $Variables = null)
        {
            ob_start();

            require $this->ViewsDirectory . "$View.phtml";
            $Content = ob_get_contents();

            ob_clean();

            if($this->RenderIn == "head")
            {
                $this->HeadContent .= $Content;
            }
            else
            {
                $this->BodyContent .= $Content;
            }
        }

        /**
         * Renders plain text.
         * 
         * @param string $Text Text to render.
         * @return void
         * 
         * @since 1.0.0
         */
        public function RenderText(string $Text)
        {
            $this->RenderFrame = false;
            $this->BodyContent .= $Text;
        }

        /**
         * Renders multiple views.
         * 
         * @param array $ViewsVariables File path for views and variables.
         * @return void
         * 
         * @since 1.0.0
         */
        public function MultipleRender(array $ViewsVariables)
        {
            foreach($ViewsVariables as $Value)
            {
                $Render = $Value[0];
                $Variables = (!empty($Value[1])) ? $Value[1] : null;

                $this->Render($Render, $Variables);
            }
        }

        /**
         * Renders main frame at destruct.
         * @return void
         * 
         * @since 1.0.0
         */
        public function __destruct()
        {
            if($this->RenderFrame)
            {
                ob_start();
                require $this->ViewsDirectory . $this->Frame . ".phtml";
                $Content = ob_get_contents();
                ob_clean();
    
                //Tidy is just beatiful html format.
                $TidyConfig =
                [
                    "indent"=>true,
                    "indent-spaces"=>4,
                    "wrap"=>0,
                    "drop-empty-elements"=>false,
                    "drop-empty-paras"=>false
    
                ];
                $Tidy = new tidy();
                $Tidy->parseString($Content, $TidyConfig, "utf8");
                $Tidy->cleanRepair();
    
                if($this->MinifyOutput)
                {
                    print minify_html($Tidy);
                }
                else
                {
                    echo $Tidy;
                }
            }
            else
            {
                print $this->BodyContent;
            }
        }
    }
}
?>
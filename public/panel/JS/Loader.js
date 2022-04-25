var Panel = {};
Panel.Loader =
{
    Loader:undefined,
    Message: undefined,
    Open: function()
    {
        this.Message.innerText = this.GetMessage();
        document.body.classList.add("Lock");
        this.Loader.classList.add("Active");
    },
    Close: function()
    {
        document.body.classList.remove("Lock");
        this.Loader.classList.remove("Active");
    },
    GetMessage: function()
    {
        let Messages =
        [
            "Biraz bekleticem canısı..",
            "Bende beklemeyi sevmiyorum ama işte naaparsın..",
            "Yalnız bu duvarın rengide amma güzelmiş!",
            "Su molası!",
            "Yaz geldi her yerde çiçekler, sen burada blog yazısını bekler.",
            "1 koyun 2 koyun 3 koyun...",
            "Çok önemli veriler indiriliyor.",
            "Bir süre sonra hep aynı mesajları görecek olman üzücü..",
            "O gemi bir gün gelecek!",
            "Türkiye standartlarında anca bu kadar oluyor.",
            "Emrinize amade!",
            "Umuyorum ki yüklenme süreleri o kadar kısa olsun ve sen bu yazıları göremeyesin."
        ];
        let Rand = Math.floor(Math.random() * Messages.length);
        return Messages[Rand];
    }
}

$(document).ready(function()
{
    Panel.Loader.Loader = document.getElementById("Loader");
    Panel.Loader.Message = document.querySelector("#Loader .Message");
});
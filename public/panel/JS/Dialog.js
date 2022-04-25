Panel.Dialog =
{
    Pop: undefined,
    Close: undefined
}

Panel.Dialog.Pop = function(Title, Message, OnConfirm)
{
    let Dialog = document.querySelector(".Dialog");
    
    Dialog.querySelector(".Head h1").innerText = Title;
    Dialog.querySelector(".Body p").innerText = Message;

    let ConfirmFunction = function()
    {
        this.removeEventListener("click", ConfirmFunction);
        Panel.Dialog.Close();
        OnConfirm();
    };

    Dialog.querySelector("button.Confirm").addEventListener("click", ConfirmFunction);
    Dialog.querySelector("button.Cancel").addEventListener("click", Panel.Dialog.Close);
    document.body.classList.add("Lock");
    Dialog.classList.add("Active");
};

Panel.Dialog.Close = function()
{
    document.querySelector(".Dialog").classList.remove("Active");
    document.body.classList.remove("Lock");
};
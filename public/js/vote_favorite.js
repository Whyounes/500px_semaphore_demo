$(function(){
    $('body').on('click', '.thumb .vote', function(e){
        e.preventDefault();
        $this = $(this);
        var pid = $this.parents(".thumb").data("photo-id");

        $.ajax({
            url: "/ajax/photo/vote",
            type: "POST",
            dataType: "json",
            data: {
                pid: pid
            },
            success: function(data){
                if(data.hasOwnProperty("error")){
                    alert(data.error);
                }
                else{
                    $this.text(data.photo.votes_count);
                    alert("Photo voted successfully");
                }
            }
        });
    });

    $('body').on('click', '.thumb .favorite', function(e){
        e.preventDefault();
        $this = $(this);
        var pid = $this.parents(".thumb").data("photo-id");

        $.ajax({
            url: "/ajax/photo/favorite",
            type: "POST",
            dataType: "json",
            data: {
                pid: pid
            },
            success: function(data){
                if(data.hasOwnProperty("error")){
                    alert(data.error);
                }
                else{
                    $this.text(data.photo.favorites_count);
                    alert("Photo favourited successfully");
                }
            }
        });
    });
});
var $overlay = $('<div id="overlay"></div>');
var $image = $("<img>");
var $caption = $("<p></p>");

$overlay.append($image);
$overlay.append($caption);

$("body").append($overlay);

$("ul#projectList li img.projectItem").click(function(event) {
    event.preventDefault();

    var imageLocation = $(this).attr("src");
    var imageAltText = $(this).attr("alt");
    var applicationLocation = $(this).next("a").attr("href");
    var $applicationLink = $("<a></a>");
    
    $applicationLink.attr("href", applicationLocation);
    $applicationLink.text(applicationLocation);

    $image.attr("src", imageLocation);
    $image.attr("alt", imageAltText);
    
    $caption.html("Application Link: ");
    $caption.append($applicationLink);

    $overlay.show();
});

$overlay.click(function() {
    $overlay.hide();
});

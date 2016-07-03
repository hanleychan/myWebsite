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
    var applicationDescription = $(this).next("a").children("p").html() + "<br><br>";
    var applicationTitle = $(this).prev("a").children("h4").html();
    var $applicationTitleSpan = $('<span class="projectTitle"></span>');
    var $applicationLink = $("<a></a>");

    $applicationLink.attr("href", applicationLocation);
    $applicationLink.text(applicationLocation);
    $applicationTitleSpan.text(applicationTitle);

    $image.attr("src", imageLocation);
    $image.attr("alt", imageAltText);
    
    $caption.html("");
    $caption.append($applicationTitleSpan);
    $caption.append("<br>");
    $caption.append($applicationLink);
    $caption.append("<br><br>");
    $caption.append(applicationDescription);

    $overlay.show();
});

$overlay.click(function() {
    $overlay.hide();
});


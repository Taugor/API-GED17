var express = require('express');
var app = express();
var port = 8084;
app.use(express.static("myApp")); // myApp will be the same folder name.
app.get('/', function (req, res,next) {
 res.redirect('/'); 
});
app.listen(port, "192.168.0.16");
console.log("MyProject Server is Listening on port " + port);
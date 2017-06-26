

var app = require('express')();
var server = require('http').Server(app);
var users = require('./node_app/users.js')();
var pingInterval = 30 * 1000;
var Socket = require('socket.io');
var io = Socket(server, {'pingInterval': pingInterval, 'pingTimeout': 6000});

//get ip address and path
var os = require("os");
var interfaces = os.networkInterfaces();
var addresses = [];
for (var k in interfaces) {
    for (var k2 in interfaces[k]) {
        var address = interfaces[k][k2];
        if (address.family === 'IPv4' && !address.internal) {
            addresses.push(address.address);
        }
    }
}
var path = "/" + require('path').basename(__dirname);
var hostname = addresses[0];
var webPort = 80;
//here is where you can change the main port mine is 8888 as default of web app it will be 80

var serverPort;
var app_key_secret;
var debugging_mode;
/**
 * You can control those variables as you want
 */
getHostname();

function getHostname() {
    var http = require('http');
    var options = {
        hostname: hostname,
        port: webPort,
        path: path + '/getServerInfo',
        method: 'GET',
        type: "json",
        headers: {
            'Content-Type': 'application/json'
        }
    };

    var buffer = "";
    var req = http.request(options, function (res) {
        res.on('data', function (chunk) {
            buffer += chunk;
        });
        res.on('end', function () {

            var obj = JSON.parse(buffer);
            if (obj.success) {
                app_key_secret = obj.app_key_secret; //must be the same for the android project key secret()
                debugging_mode = obj.debugging_mode; // for this you set it false if you don't want to see logs of your server


                serverPort = obj.serverPort;
                if (serverPort != null) {

                    var port = process.env.PORT || serverPort;

                    /**
                     * server listener
                     */
                    server.listen(port, function () {
                        console.log('Server listening at port %d', port);
                    });
                }

                /**
                 * this for check if the user connect from the app
                 */
                io.use(function (socket, next) {
                    var token = socket.handshake.query.token;
                    if (token === app_key_secret) {
                        if (debugging_mode) {
                            console.log("token valid  authorized", token);
                        }
                        next();
                    } else {
                        if (debugging_mode) {
                            console.log("not a valid token Unauthorized to access");
                        }
                        next(new Error("not valid token"));
                    }
                });

                /**
                 * Socket.io event handling
                 */
                require('./node_app/socketHandler.js')(io, users, debugging_mode, path, hostname, os, webPort, pingInterval);

            } else {

            }


        });

        res.on('error', function (e) {
            console.log("Got error: " + e.message);
        });
    });
    req.end();

}



module.exports = function (io, users, debugging_mode, path, hostname, os, mainPort, pingInterval) {

    io.on('connection', function (socket) {

        /**
         * Ping/Pong methods
         * */

        socket.on('socket_pong', function (data) {
            // console.log("Pong received from client");
        });

        setTimeout(sendHeartbeat, pingInterval);

        function sendHeartbeat() {

            setTimeout(sendHeartbeat, pingInterval);
            io.sockets.emit('socket_ping', {beat: 1});
        }

        /******************************************** Method for groups  ********************************************************************************
         *
         * **********************************************************************************************************************************************
         */

        /**
         * method to check if  member of group  is start typing
         */
        socket.on('socket_member_typing', function (data) {
            io.sockets.emit('socket_member_typing', {
                recipientId: data.recipientId,
                groupId: data.groupId,
                senderId: data.senderId
            });
        });

        /**
         * method to check if a member of group  is stop typing
         */
        socket.on('socket_member_stop_typing', function (data) {
            io.sockets.emit('socket_member_stop_typing', {
                recipientId: data.recipientId,
                groupId: data.groupId,
                senderId: data.senderId
            });
        });
        /**
         * method to check if u receive a new message
         */
        socket.on('socket_new_group_message', function (data) {
            io.sockets.emit('socket_new_group_message_server', {
                recipientId: data.recipientId,
                messageId: data.messageId,
                messageBody: data.messageBody,
                senderId: data.senderId,
                phone: data.phone,
                senderName: data.senderName,
                GroupImage: data.GroupImage,
                GroupName: data.GroupName,
                groupID: data.groupID,
                date: data.date,
                isGroup: data.isGroup,
                image: data.image,
                video: data.video,
                audio: data.audio,
                document: data.document,
                thumbnail: data.thumbnail,
                duration: data.duration,
                fileSize: data.fileSize
            });

        });

        /**
         * mehtod to save firstly the message in the database
         */
        socket.on('socket_save_group_message', function (data, callback) {
            var http = require('http');
            var queryString = require("querystring");
            var qs = queryString.stringify(data);
            var qslength = qs.length;
            var options = {
                hostname: hostname,
                port: mainPort,
                path: path + '/Groups/saveMessage',
                method: 'POST',
                type: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'token': data.userToken,
                    'Content-Length': qslength
                }
            };

            var buffer = "";
            var req = http.request(options, function (res) {
                res.on('data', function (chunk) {
                    buffer += chunk;
                });
                res.on('end', function () {

                    var messageData = {
                        messageId: buffer
                    };
                    if (debugging_mode) {
                        console.log(messageData);
                    }
                    callback(messageData);
                    socket.emit('socket_group_sent', {
                        groupId: data.groupID,
                        senderId: data.senderId
                    });
                });

                res.on('error', function (e) {
                    console.log("Got error: " + e.message);
                });
            });

            req.write(qs);
            req.end();
        });

        /**
         * method to notify all members
         */
        socket.on('socket_groupImageUpdated', function (dataString) {

            if (debugging_mode) {
                console.log("socket_groupImageUpdated ");
            }
            io.sockets.emit('socket_groupImageUpdated', dataString);
        });
        /**
         * method to ping and check if member of group is connected
         */
        socket.on('socket_user_ping_group', function (data) {

            var pingedData;

            pingedData = {
                recipientId: data.recipientId,
                messageId: data.messageId,
                messageBody: data.messageBody,
                senderId: data.senderId,
                phone: data.phone,
                senderName: data.senderName,
                GroupImage: data.GroupImage,
                GroupName: data.GroupName,
                groupID: data.groupID,
                date: data.date,
                isGroup: data.isGroup,
                image: data.image,
                video: data.video,
                audio: data.audio,
                document: data.document,
                thumbnail: data.thumbnail,
                duration: data.duration,
                fileSize: data.fileSize,
                pinged: data.pinged,
                pingedId: data.pingedId
            };
            socket.emit('socket_user_pinged_group', pingedData);

        });

        /**
         * method to send message group
         */
        socket.on('socket_send_group_message', function (dataString) {
            if (debugging_mode) {
                console.log("data group sended" + dataString);
            }
            saveMessageGroupToDataBase(dataString);
        });

        /**
         * method to save message as waiting
         * @param data
         */
        function saveMessageGroupToDataBase(data) {

            var http = require('http');
            var queryString = require("querystring");
            var qs = queryString.stringify(data);
            var qslength = qs.length;
            var options = {
                hostname: hostname,
                port: mainPort,
                path: path + '/Groups/send',
                method: 'POST',
                type: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'token': data.userToken,
                    'Content-Length': qslength
                }
            };

            var buffer = "";
            var req = http.request(options, function (res) {
                res.on('data', function (chunk) {
                    buffer += chunk;
                });
                res.on('end', function () {
                    socket.emit('socket_group_delivered', {
                        groupId: data.groupID,
                        senderId: data.senderId

                    });
                });

                res.on('error', function (e) {
                    console.log("Got error: " + e.message);
                });
            });

            req.write(qs);
            req.end();


        }

        /**
         * method to check if there is messages to sent
         * @param data
         */
        function CheckForUnsentMessages(data) {

            var http = require('http');
            var queryString = require("querystring");
            var qs = queryString.stringify(data);
            var qslength = qs.length;

            var options = {
                hostname: hostname,
                port: mainPort,
                path: path + '/Groups/checkUnsentMessageGroup',
                method: 'POST',
                type: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'token': data.userToken,
                    'Content-Length': qslength
                }
            };

            var body = "";
            var req = http.request(options, function (res) {
                res.on('data', function (chunk) {
                    body += chunk;
                });
                res.on('end', function () {
                    var obj = JSON.parse(body);
                    for (var i = 0; i < obj.length; i++) {
                        var pingedData = {
                            recipientId: obj[i].recipientId,
                            messageId: obj[i].messageId,
                            messageBody: obj[i].messageBody,
                            senderId: obj[i].senderId,
                            phone: obj[i].phone,
                            senderName: obj[i].senderName,
                            GroupImage: obj[i].GroupImage,
                            GroupName: obj[i].GroupName,
                            groupID: obj[i].groupId,
                            date: obj[i].date,
                            isGroup: obj[i].isGroup,
                            image: obj[i].image,
                            video: obj[i].video,
                            audio: obj[i].audio,
                            document: obj[i].document,
                            thumbnail: obj[i].thumbnail,
                            pinged: obj[i].pinged,
                            pingedId: obj[i].pingedId,
                            duration: obj[i].duration,
                            fileSize: obj[i].fileSize
                        };
                        socket.emit('socket_user_pinged_group', pingedData);

                    }
                });
                res.on('error', function (e) {
                    console.log("Got error: " + e.message);
                });
            });

            req.write(qs);
            req.end();


        }


        /******************************************** Method for a single user ********************************************************************************
         *
         * ****************************************************************************************************************************************************
         */

        /**
         * method to save user as connected
         */
        socket.on('socket_user_connect', function (data) {
            if (debugging_mode) {
                console.log("the user with id " + data.connectedId + " connected " + +data.connected + " token " + data.userToken + "socket.id " + socket.id);
            }
            if (data.connectedId != null && data.connectedId != 0) {
                var user = users.getUser(data.connectedId);
                if (user != null) {
                    users.updateUser(data.connectedId, data.connected, socket.id);
                } else {
                    users.addUser(data.connectedId, data.connected, socket.id);
                }

                io.sockets.emit('socket_user_connect', {
                    connectedId: data.connectedId,
                    connected: true,
                    socketId: socket.id
                });

            }


        });


        /**
         * method if a user is disconnect from sockets
         * and then remove him from array of current users connected
         */
        socket.on('disconnect', function () {
            var usersArray = users.getUsers();
            if (usersArray.length != 0) {
                for (var i = 0; i < usersArray.length; i++) {
                    var user = usersArray[i];
                    if (user != null) {

                        if (user.socketID == socket.id) {
                            if (debugging_mode) {
                                console.log("the user with id  " + user.ID + " is disconnect 1 ");
                            }
                            io.sockets.emit('socket_user_connect', {
                                connectedId: user.ID,
                                connected: false,
                                socketId: user.socketID
                            });

                            users.removeUser(user.ID);
                            if (debugging_mode) {
                                console.log("the users list size disconnect " + usersArray.length);
                            }
                            break;
                        }

                    } else {
                        if (debugging_mode) {
                            console.log("the user is null disconnect ");
                        }
                    }


                }
            }
        });

        socket.on('socket_user_disconnect', function (data) {

            if (data.connectedId != null && data.connectedId != 0) {
                if (debugging_mode) {
                    console.log("the user with id  " + data.connectedId + " is disconnect  2");
                }

                var user = users.getUserBySocketID(data.socketId);
                if (user != null) {

                    io.sockets.emit('socket_user_connect', {
                        connectedId: user.ID,
                        connected: false,
                        socketId: user.socketID
                    });

                    users.removeUser(user.ID);

                }
            }
        });
        /**
         * method to notify all users by the new user joined
         */
        socket.on('socket_new_user_has_joined', function (dataString) {
            var userData = {
                phone: dataString.phone,
                senderId: dataString.senderId
            };
            io.sockets.emit('socket_new_user_has_joined', {
                phone: userData.phone,
                senderId: userData.senderId
            });
        });

        /**
         * method to notify all users
         */
        socket.on('socket_profileImageUpdated', function (dataString) {

            if (debugging_mode) {
                console.log("socket_profileImageUpdated ");
            }
            var userData = {
                phone: dataString.phone,
                senderId: dataString.senderId
            };
            io.sockets.emit('socket_profileImageUpdated', {
                phone: userData.phone,
                senderId: userData.senderId
            });
        });
        /**
         * method to get response from recipient to update status (from waiting to sent )
         */
        socket.on('socket_send_message', function (dataString) {
            var messageID = {
                messageId: dataString.messageId,
                senderId: dataString.senderId
            };
            io.sockets.emit('socket_send_message', {
                messageId: messageID.messageId,
                senderId: messageID.senderId
            });
        });

        /**
         * method to check if user disconnected  before send a message to him (do a ping and get a callback)
         */
        socket.on('socket_user_ping', function (data, callback) {
            var pingingData = {
                pinged: data.pinged,
                pingedId: data.pingedId,
                socketId: data.socketId
            };

            var pingedData;

            if (pingingData.pingedId = data.recipientId && pingingData.pinged == true) {
                pingedData = {
                    messageId: data.messageId,
                    senderImage: data.senderImage,
                    pingedId: data.recipientId,
                    pinged: pingingData.pinged,
                    senderId: data.senderId,
                    recipientId: data.recipientId,
                    senderName: data.senderName,
                    messageBody: data.messageBody,
                    date: data.date,
                    isGroup: data.isGroup,
                    conversationId: data.conversationId,
                    image: data.image,
                    video: data.video,
                    audio: data.audio,
                    document: data.document,
                    thumbnail: data.thumbnail,
                    phone: data.phone,
                    socketId: data.socketId,
                    duration: data.duration,
                    fileSize: data.fileSize
                };
            } else {
                pingedData = {
                    messageId: data.messageId,
                    senderImage: data.senderImage,
                    pingedId: data.senderId,
                    pinged: pingingData.pinged,
                    senderId: data.senderId,
                    recipientId: data.recipientId,
                    senderName: data.senderName,
                    messageBody: data.messageBody,
                    date: data.date,
                    isGroup: data.isGroup,
                    conversationId: data.conversationId,
                    image: data.image,
                    video: data.video,
                    audio: data.audio,
                    document: data.document,
                    thumbnail: data.thumbnail,
                    phone: data.phone,
                    socketId: data.socketId,
                    duration: data.duration,
                    fileSize: data.fileSize
                };
            }
            callback(pingedData);
            //  return;
        });
        /**
         * method to check if u receive a new message
         */
        socket.on('socket_new_message', function (data) {
            if (debugging_mode) {
                console.log("users connected list size is " + users.getUsers().length);
                console.log("new message is " + data.messageBody + " From user with id " + data.senderId + " to user with id " + data.recipientId);
            }
            var user = users.getUser(data.recipientId);
            if (user != null) {
                console.log("user not null ");
                console.log("socket id " + user.socketID);
                socket.to(user.socketID).emit('socket_new_message_server', {
                    messageId: data.messageId,
                    senderImage: data.senderImage,
                    senderId: data.senderId,
                    recipientId: data.recipientId,
                    senderName: data.senderName,
                    messageBody: data.messageBody,
                    date: data.date,
                    isGroup: data.isGroup,
                    conversationId: data.conversationId,
                    image: data.image,
                    video: data.video,
                    audio: data.audio,
                    document: data.document,
                    thumbnail: data.thumbnail,
                    phone: data.phone,
                    duration: data.duration,
                    fileSize: data.fileSize
                });
            } else {
                console.log("user is null ");
            }


        });

        /**
         * method to save new message to database
         */
        socket.on('socket_save_new_message', function (data) {
            saveMessageToDataBase(data);
        });
        /**
         * method to save message of user
         * @param data
         */
        function saveMessageToDataBase(data) {
            var http = require('http');
            var queryString = require("querystring");
            var qs = queryString.stringify(data);
            var qslength = qs.length;
            var options = {
                hostname: hostname,
                port: mainPort,
                path: path + '/Messages/send',
                method: 'POST',
                type: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'token': data.userToken,
                    'Content-Length': qslength
                }
            };

            var buffer = "";
            var req = http.request(options, function (res) {
                res.on('data', function (chunk) {
                    buffer += chunk;
                });

                res.on('error', function (e) {
                    console.log("Got error: " + e.message);
                });
            });

            req.write(qs);
            req.end();

        }


        /**
         * method to check if user is start typing
         */
        socket.on('socket_typing', function (data) {
            var user = users.getUser(data.recipientId);
            if (user != null) {
                socket.to(user.socketID).emit('socket_typing', {
                    recipientId: data.recipientId,
                    senderId: data.senderId
                });
            }
        });

        /**
         * method to check if user is stop typing
         */
        socket.on('socket_stop_typing', function (data) {

            var user = users.getUser(data.recipientId);
            if (user != null) {
                socket.to(user.socketID).emit('socket_stop_typing', {
                    recipientId: data.recipientId,
                    senderId: data.senderId
                });

            }
        });

        /**
         * method to check status last seen
         */
        socket.on('socket_last_seen', function (data) {

            var user = users.getUser(data.recipientId);
            if (user != null) {
                socket.to(user.socketID).emit('socket_last_seen', {
                    lastSeen: data.lastSeen,
                    senderId: data.senderId,
                    recipientId: data.recipientId
                });
            }
        });


        /**
         * method to check if user is read (seen) a specific message
         */
        socket.on('socket_seen', function (data) {
            var user = users.getUser(data.recipientId);
            if (user != null) {
                socket.to(user.socketID).emit('socket_seen', {
                    senderId: data.senderId,
                    recipientId: data.recipientId
                });
            }
        });

        /**
         * method to check if a message is delivered to the recipient
         */
        socket.on('socket_delivered', function (data) {
            io.sockets.emit('socket_delivered', {
                messageId: data.messageId,
                senderId: data.senderId
            });
        });

        /**
         * method to check if recipient is Online
         */
        socket.on('socket_is_online', function (data) {
            io.sockets.emit('socket_is_online', {
                senderId: data.senderId,
                connected: data.connected
            });
        });


        /******************************************** Method for calls ********************************************************************************
         *
         * ****************************************************************************************************************************************************
         */


        /**
         * method to check if user is connected  before call him (do a ping and get a callback)
         */
        socket.on('socket_call_user_ping', function (data, callback) {
            console.log("socket_call_user_ping called ");
            var user = users.getUser(data.recipientId);
            var pingedData;
            if (user != null) {
                console.log("socket id " + user.socketID);
                pingedData = {
                    socketId: user.socketID,
                    recipientId: user.socketID,
                    connected: true
                };
                callback(pingedData);
            }

        });

        /**
         * method to check if user is already on users array
         */
        socket.on('reset_socket_id', function (data, callback) {
            console.log("reset_socket_id called " + data.userSocketId);
            var pingedData = {
                userSocketId: data.userSocketId
            };
            callback(pingedData);
            /*var user = users.getUserBySocketID(data.userSocketId);
             var pingedData;
             if (user != null) {
             console.log("reset_socket_id not null " + user.socketID);
             pingedData = {
             userSocketId: data.userSocketId
             };
             callback(pingedData);
             } else {
             console.log("user is null  reset_socket_id function ");
             }*/
        });

        /**
         * method make the connection between the too peer
         */
        socket.on('signaling_server', function (data) {
            console.log("signaling_server called " + data.to);
            var user = users.getUserBySocketID(data.to);
            var socketId = data.to;
            //if (user != null) {
            delete data.to;
            socket.to(socketId).emit('signaling_server', data);
            /* } else {
             console.log("user is null  signaling_server function ");
             //kolo adasnt skergh bach ighiga null nrj3 request bach ndir dialog this person is not available like whatsapp
             }*/

        });

        var makeCall = function (data) {
            console.log("make_new_call function " + data.to);
            /*   var user = users.getUserBySocketID(data.to);
             if (user != null) {*/
            socket.to(data.to).emit('receive_new_call', data);
            /* } else {
             console.log("user is null  make_new_call function ");
             //kolo adasnt skergh bach ighiga null nrj3 request bach ndir dialog this person is not available like whatsapp
             }*/

        };
        /**
         * method to initialize the new call
         */
        socket.on('make_new_call', makeCall);

        /**
         * method to Reject a call
         */
        socket.on('reject_new_call', function (data) {
            console.log("reject_new_call function ");
            /*  var user = users.getUserBySocketID(data.callerSocketId);
             if (user != null) {*/
            socket.to(data.callerSocketId).emit("reject_new_call", data);
            /* } else {
             console.log("user is null reject_new_call function ");
             }*/
        });


        /**
         * method to Accept a call
         */
        socket.on('accept_new_call', function (data) {
            console.log("accept_new_call function ");
            /*var user = users.getUserBySocketID(data.callerSocketId);
             if (user != null) {*/
            socket.to(data.callerSocketId).emit("accept_new_call", data);
            /*} else {
             console.log("user is null  accept_new_call function ");

             }*/
        });
        /**
         * method to HangUp a call
         */
        socket.on('hang_up_call', function (data) {
            console.log("hang_up_call function ");
            /* var user = users.getUserBySocketID(data.callerSocketId);
             if (user != null) {*/
            socket.to(data.callerSocketId).emit("hang_up_call", data);
            /*} else {
             console.log("user is null  hang_up_call function ");
             }*/
        });


    });
};
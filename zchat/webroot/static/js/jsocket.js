/* jSocket.js
 *
 * The MIT License
 *
 * Copyright (c) 2008 Tjeerd Jan 'Aidamina' van der Molen <aidamina@gmail.com>
 * http://jsocket.googlecode.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Construct
 * @param {function} onReady When the SWF is added the to document and ready for use
 * @param {function} onConnect Connection attempt finished (either succesfully or with an error)
 * @param {function} onData Socket received data from the remote host
 * @param {function} onClose Remote host disconnects the connection
 */
function jSocket(onReady, onConnect, onData, onClose) {
    this.onReady = onReady;
    this.onConnect = onConnect;
    this.onData = onData;
    this.onClose = onClose;

    this.id = "jSocket_" + (++jSocket.last_id);
    jSocket.sockets[this.id] = this;

    // Connection state
    this.connected = false;
}

/**
 * String defining the default swf file
 * @var String
 */
jSocket.swf = "jsocket.swf";

/**
 * Object used as array with named keys to
 * keep references to the instantiated sockets
 * @var Object
 */
jSocket.sockets = {};

/**
 * Id used to generate a unique id for the embedded swf
 * @var int
 */
jSocket.last_id = 0;

/**
 * A nonexisting public flash object variable
 * This variable is used for testing access to the object.
 * @var String
 */
jSocket.variableTest = 'xt';

/**
 * Find the SWF in the DOM and return it
 * @return DOMNode
 */
jSocket.prototype.findSwf = function () {
    return document.getElementById(this.target);
}

/**
 * Insert the SWF into the DOM
 * @param String {target} The id of the DOMnode that will get replaced by the SWF
 * @param String {swflocation} The filepath to the SWF
 */
jSocket.prototype.setup = function (target, swflocation) {
    if (typeof(swfobject) == 'undefined')
        throw 'SWFObject not found! Please download from http://code.google.com/p/swfobject/';
    if (typeof(this.target) != 'undefined')
        throw 'Can only call setup on a jSocket Object once.';
    this.target = target;

    // Add the object to the dom
    return swfobject.embedSWF(
        (swflocation ? swflocation : jSocket.swf) + '?' + this.id,
        this.target,
        '0', // width
        '0', // height
        '9.0.0',
        'expressInstall.swf',
        // Flashvars
        {},
        // Params
        {'menu': 'false'},
        // Attributes
        {}
    );

}

/**
 * Connect to the specified host on the specified port
 * @param String {host} Hostname or ip to connect to
 * @param Int {port} Port to connect to on the given host
 */
jSocket.prototype.connect = function (host, port) {
    if (!this.movie)
        throw "jSocket isn't ready yet, use the onReady event";
    if (this.connected)
        this.movie.close();
    this.movie.connect(host, port);
}

/**
 * Close the current socket connection
 */
jSocket.prototype.close = function () {
    this.connected = false;
    if (this.movie)
        this.movie.close();
}

/**
 * Send data trough the socket to the server
 * @param Mixedvar {data} The data to be send to the sever
 */
jSocket.prototype.write = function (data) {
    this.assertConnected();
    this.movie.write(data);
}

/**
 * Make sure the socked is connected.
 * @throws Exception Throws an exception when the socket isn't connected
 */
jSocket.prototype.assertConnected = function () {
    if (!this.connected || !this.movie)
        throw "jSocket is not connected, use the onConnect event ";
}

/**
 * Callback that the flash object calls using externalInterface
 * @param String {name} What callback is called
 * @param String {id} Id of the socket
 * @param String {data} Used for data and errors
 */
jSocket.flashCallback = function (name, id, data) {
    // Because the swf locks up untill the callback is done executing we want to get this over with asap!
    // http://www.calypso88.com/?p=25
    var f = function () {
        jSocket.executeFlashCallback(name, id, data);
    };
    setTimeout(f, 0);
    return;
}

/**
 * Execute the Callbacks
 * @param String {name} What callback is called
 * @param String {id} Id of the socket
 * @param String {data} Used for data and errors
 */
jSocket.executeFlashCallback = function (name, id, data) {
    var socket = jSocket.sockets[id];

    switch (name) {
        // Callback for the flash object to signal the flash file is loaded
        // triggers jsXMLSocket.onReady
        case 'init':
            var v = jSocket.variableTest;
            // Wait until we can actually set Variables in flash
            var f = function () {
                var err = true;
                try {
                    // Needs to be in the loop, early results might fail, when DOM hasn't updated yet
                    var m = socket.findSwf();
                    m.SetVariable(v, 't');
                    if ('t' != m.GetVariable(v))
                        throw null;
                    m.SetVariable(v, '');
                    // Store the found movie for later use
                    socket.movie = m;
                    err = false;
                } catch (e) {
                    setTimeout(f, 0);
                }
                // Fire the onReady event
                if (!err && typeof socket.onReady == "function")
                    socket.onReady();
            };
            setTimeout(f, 0);
            break;

        // Callback for the flash object to signal data is received
        // triggers jSocket.onData
        case 'data':
            if (typeof socket.onData == "function")
                socket.onData(data);
            break;

        // Callback for the flash object to signal the connection attempt is finished
        // triggers jSocket.onConnect
        case 'connect':
            socket.connected = true;
            if (typeof socket.onConnect == "function")
                socket.onConnect(true);
            break;

        // Callback for the flash object to signal the connection attempt is finished
        // triggers jSocket.onConnect
        case 'error':
            if (typeof socket.onConnect == "function")
                socket.onConnect(false, data);
            break;

        // Callback for the flash object to signal the connection was closed from the other end
        // triggers jSocket.onClose
        case 'close':
            socket.connected = false;
            if (typeof socket.onClose == "function")
                socket.onClose();
            break;

        default:
            throw "jSocket: unknown callback '" + name + "' used";
    }
}

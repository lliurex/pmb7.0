/*--------------------------------------------------|
| dTree 2.05 | www.destroydrop.com/javascript/tree/ |
|---------------------------------------------------|
| Copyright (c) 2002-2003 Geir Landr�               |
|                                                   |
| This script can be used freely as long as all     |
| copyright messages are intact.                    |
|                                                   |
| Updated: 17.04.2003                               |
|--------------------------------------------------*/

// Node object
	this.id = id;
};

// Tree object
	this.config = {
	}
	this.icon = {
		root		: '../../images/vide.png',
	};
	this.obj = objName;
// Adds a new node to the node array
	this.aNodes[this.aNodes.length] = new Node(id, pid, name, attr, url, title, target, icon, iconOpen, open);
// Open/close all nodes
dTree.prototype.closeAll = function() {
	this.oAll(false);
// Outputs the tree to the page
	var str = '<div class="dtree">\n';
// Creates the tree structure
	var str = '';

// Creates the node icon, url and text
	var str = '<div class="dTreeNode">' + this.indent(node, nodeId);

	var str = '';
// Checks if a node has any children and if it is the last sibling
	var lastId;

// Returns the selected node
	var sn = this.getCookie('cs' + this.obj);

// Highlights the selected node
	if (!this.config.useSelection) return;

// Toggle Open or close
	var cn = this.aNodes[id];

// Open or close all nodes
	for (var n=0; n<this.aNodes.length; n++) {

// Opens the tree to a specific node
dTree.prototype.openTo = function(nId, bSelect, bFirst) {
	if (!bFirst) {

// Closes all nodes on the same level as certain node
	for (var n=0; n<this.aNodes.length; n++) {
// Closes all children of a node
	for (var n=0; n<this.aNodes.length; n++) {
// Change the status of a node(open or closed)
	eDiv	= document.getElementById('d' + this.obj + id);


// [Cookie] Clears a cookie
	var now = new Date();

	document.cookie =

// [Cookie] Gets a value from a cookie
	var cookieValue = '';

dTree.prototype.updateCookie = function() {
	var str = '';

// [Cookie] Checks if a node id is in a cookie
dTree.prototype.isOpen = function(id) {
// If Push and pop is not implemented by the browser
if (!Array.prototype.push) {
if (!Array.prototype.pop) {
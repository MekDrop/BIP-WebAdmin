<html>
<title>BIP WebAdmin#title#</title>
<style type="text/css">
  html, * {
	  font-family: terminal;
	  margin: 0px;
  }
  body {
  	  background-color: gray;
  }
  a {
	  text-decoration: none;
  }
  a:hover {
	  text-decoration: underline;
  }
  .menu li {
	  display: inline;
  }
  .menu {
  	  left: 0px;
	  top: 0px;
	  width: 100%;
	  padding-left: 30px; 
	  border-style: solid;
	  border-width: 1px;
	  position: fixed;
	  background-color: blue;
  }
  .menu li a {
	  width: 100px;
	  background-color: blue;
	  display: block;
	  float: left;
	  text-align: center;
	  color: white;
	  text-decoration: none;
	  font-weight: bold;
  }
  .menu li a:hover {
	  background-color: black;
  }
  .menu li a.selected {
	  background-color: #808080;
  }
  .content {	  
	  margin-top: 30px;
	  margin-left: 20px;
	  margin-right: 20px;
  }
  input[type="text"] {
	  width: 250px;
  }
  input.checked {
	  background-color: #330033;
	  color:#330033;
  }
  input, select {
	  border-style: solid;
	  border-width: 1px;
	  padding: 1px;
	  padding-left: 5px;
	  opacity: 0.8;
  }
  input:active, select:active, input:hover, select:hover,input:focus, select:focus {
      opacity: 0.99;
  }
  input[type="checkbox"] {
  	  border-color: black;
	  padding: 0;
	  margin: 0;
  }
  form table tr td button {
	  border-style: solid;
	  border-width: 1px;
	  font-weight: bold;
	  background-color: black;
	  border-color: black;
	  color: gray;
	  opacity: 0.8;
  }
  form table tr td button:active, form table tr td button:hover, form table tr td button:focus {
	  opacity: 0.99;
	  color: white;
  }
  form.action {
	  display: inline;
  }
  form.action button {
	  border-style: solid;
	  border-width: 1px;
	  font-weight: bold;
	  background-color: black;
	  border-color: black;
	  color: gray;
	  opacity: 0.8;
  }
  form.action button:active, form.action button:hover, form.action button:focus {
	  opacity: 0.99;
	  color: white;
  }
  ol.list, ul.list {
	  margin-left: 30px;
	  padding: 0;
  }
  h5.list {
	  margin-bottom: 10px;
  }
  .list li {
	 list-style-type: none;
	 width: 200px;
  }
  .list a.delete {
    color: #CC0000;
	display: block;
	float: right;
  }
  .list a.name {
	width: 150px;
	display: block;
  }
  td {
	  vertical-align: top;
  }
</style>
<head>
<ul class="menu">
   <li><a href="?site=about"#current_menu_about#>About</a></li>
   <li><a href="?site=servers"#current_menu_servers#>Servers</a></li>
   <li><a href="?site=users"#current_menu_users#>Users</a></li>
   <li><a href="?site=system"#current_menu_system#>System</a></li>
   <li><a href="?site=state"#current_menu_state#>State</a></li>
</ul>
<div class="content">
   #content#
</div>
</head>
</html>
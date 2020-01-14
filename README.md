**REST Api for the sensesofcuba project**

This is a project from habana.tech


**La Autenticación va así hasta ahora**

Luego de pedir las credenciales al usuario, las envias como sigue:

	url:		/api/login	
	method:		POST	
	JSON:		{"email":"user@domain.com","password":"password"}

Si todo marcha bien, recibirá la respuesta en un JSON como el sigue:

	{
	  "token": "xxxxxxxxxxxxxxxxxx"
	}

el token recibido lo guardaremos en "localStorage.token"
de lo contrario, la respuesta es

	{
	  "code": 401,
	  "message": "Bad credentials."
	}

A partir de ese punto, puedes acceder al api, enviando la cabecera siguiente:

	"Authorization:	Bearer {token almacenado}"

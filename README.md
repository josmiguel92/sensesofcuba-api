**REST Api for the sensesofcuba project**

This is a project from habana.tech


**La Autenticación va así hasta ahora**

Luego de pedir las credenciales al usuario, las envias como sigue:

	url:		/api/login	
	method:		POST	
	JSON:		{"email":"user@domain.com","password":"password"}

Si todo marcha bien, recibirá la respuesta en un JSON como el sigue:

	{
	  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1Njg4NjEwNzMsImV4cCI6MTU2ODg2NDY3Mywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSJ9.K9ScBy1u5IX8mOqATyw8_IMaK-_PbGqNH0wjtcBlmK3_a4xdGh3y12ziy-cJBQo4LTrrjCiVNCwiGiTgV_bZfx8zRcc5VxS5BWS-OnzdHZZ5X8GWZ5hPD5iu0vO8MTiG5wecot51PSSKuIboetF1NhccPBueQXOTJVE8BRUs_z6l4_fVNHGdAvQhFoZ_irWHOh4lYqh3K9kIFio_MZaHtnTA95B1BZRFrBkq0UNU_54rk2Put4peLnxtQ1mokZsQxzsg0L_WjOqPjdKcdXOhIFh7zvHWBb86beXBQnq8q7d50iqCPcFbv2_Gw4C3qLOs-qWesQPHH-y0FZWe40-SdEMWwOQ1JKcxUO3Gy6uI9KY4bkd9kboM3O9saeAzZv8t8h55nIL1mehRKZ_6KdvhAPlmdCp57M8iJiUnEk7OHUG2nQfG4n1YpgbCWRtw9lQUEccAUgZ2NZ7TuW0H2YanMLFavgS465OI8nd4ufpH718Yp5nCkFbQKvbar5reSsLx19NBfuFzaMYOLBgZ0h07d_crTN9IwBq-Dh_wFlk3Zma33y3aU5PoNFOBNMjmS-Kh_wJlLQj7bOzPc8euF-OgudYuvA5Ik7pOgo19GjE_DYKoHvqIi1WRIgffK5sv9T30aBWOWemU-qwwsPuHI6GxCyiLUJ-EE-hEm_X6PYEvhsY"
	}

el token recibido lo guardaremos en "localStorage.token"
de lo contrario, la respuesta es

	{
	  "code": 401,
	  "message": "Bad credentials."
	}

A partir de ese punto, puedes acceder al api, enviando la cabecera siguiente:

	"Authorization:	Bearer {token almacenado}"

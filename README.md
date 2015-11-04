# yandex-disk
Создаем экземпляр класса и передаем в конструктор токен

	$app= new Disk($token)

Функция для загрузки локального файла $fileName в папку $path на яндекс диск. 

	$app->uploadFile($fileName, $path) 
	
Чтобы получить ссылку на файл нужно вызватть getFileUrl и передать в качестве параметра путь к файлу

	$app->getFileUrl ( $path );


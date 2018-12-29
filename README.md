# yandex-disk
Simple script for upload files to yandex-disk

``$app = new Disk($token)``  	

Upload file ot disk by path 

``$app->uploadFile($fileName, $path)``	 
	
Get filepath by path in yandex-disk

``$app->getFileUrl ( $path );``	
	
Delete fily by path from yandex-disk 

``$app->deleteFile($path, [true]);``




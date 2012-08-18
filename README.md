<h1>Erply Load Suppliers Example</h1>
This is a pretty simple example of how to load suppliers into Erply using the open API they provide for developers. <br /><br />

In my case, I wanted to load the suppliers from a CSV sheet and have them loaded into our Erply instance.  I quickly put this together to be able to load all our suppliers.<br /><br />

<h2>Please Note:</h2>
Be aware that Erply seems to not support uploading names that have foreign characters which are UTF-8 complient.  As an example if I try to upload, "Siléne" as the first name it will actually get cut off and only show "Sil".
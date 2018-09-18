<li><a href="/products/{{$product->id}}">General</a></li>
@if(has_role('products_prices'))
  <li><a href="/product/getPrices/{{$product->id}}">Prices</a></li>
@endif
<li><a href="/product/getAttributes/{{$product->id}}">Attributes</a></li>
<li><a href="/product/getImages/{{$product->id}}">Images</a></li>
<li><a href="/product/getDownloads/{{$product->id}}">Downloads</a></li>
<li><a href="/product/getStocks/{{$product->id}}">Stock</a></li>
<li><a href="/product/getSetup/{{$product->id}}">Setup</a></li>
@if(has_role('admin') && return_company_id() == 1)
<li><a href="/product/getSync/{{$product->id}}">Sync</a></li>
@endif

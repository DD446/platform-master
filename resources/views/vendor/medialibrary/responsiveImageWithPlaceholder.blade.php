<img{!! $attributeString !!} srcset="{{ $media->getSrcset($conversion) }}" onload="this.onload=null;this.sizes=Math.ceil(this.getBoundingClientRect().width/window.innerWidth*100)+'vw';" sizes="1px" src="{{ $media->getUrl($conversion) }}" width="{{ $width }}" class="bg-image opacity-60">
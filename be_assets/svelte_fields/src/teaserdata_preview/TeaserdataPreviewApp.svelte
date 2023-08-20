<script>
  import { onMount, tick } from "svelte";

  export let props;

  // export let helpers;

  let data = props.data;

  import Inspect from 'svelte-inspect';


  let img_url=null;
  let image_id=null;

  let title="";
  let text="";
  
  let cached_img_urls={}

  function breakize(str) {
    return str.replaceAll("~","&shy;") 
  }

$: {

   console.log('#log TDATA', props.data);


  //IMAGE:
  if(props.data.ext.mavu_teaserdata.image && props.data.ext.mavu_teaserdata.image.id) {
    image_id=props.data.ext.mavu_teaserdata.image.id
  } else if(props.data.ext.mavu_teaserdata.auto_image && props.data.ext.mavu_teaserdata.auto_image.id) {
    image_id=props.data.ext.mavu_teaserdata.auto_image.id
  } else {
    image_id=null
  }

  if (image_id) {
	  getCachedImageUrlForId(image_id)
  } else {
    img_url=null
  }

  //TITLE:
  title =props.data.ext.mavu_teaserdata.title

  if(title==null || title=="") {
    title=props.data.title
  }

  //TEXT:
  text =props.data.ext.mavu_teaserdata.text

  if(text==null || text=="") {
    text=props.data.ext.mavu_teaserdata.auto_text
  }

  title=breakize(title)
  text=breakize(text)

  
}





  onMount((args) => {
    // console.log("#log 6113 SVELTE LOADED TEASER_PREVIEW ➜ props", props);
    // console.log("#log 6113 SVELTE LOADED TEASER_PREVIEW ➜ helpers", helpers);

  
  });

  function getCachedImageUrlForId(id) {
    if(cached_img_urls[id]) {
      img_url=cached_img_urls[id]
    } else {
      getImageUrlForId(id) 
    }
  }

  function getImageUrlForId(id) {

    const url = `/admin/api/media/${id}?locale=en`;

    fetch(url)
      .then((response) => response.json())
      .then((payload) => {
        cached_img_urls[id]=payload.thumbnails['mavu_preview'];
        img_url=cached_img_urls[id]
      });

  }

</script>

<div class="tw">
  <div class="p-2 teaserdata_preview-wrapper bg-gray-50">
    <div class="teaserdata_preview">
      
      <div class="flex gap-3">
        <div class="w-[100px] flex-none">
          {#if img_url }
              <img src={img_url} />
          {/if}
        </div>
        <div  class="">
          <div class="mb-4 -mt-1 text-base font-bold">{@html title}</div>
          <div class="text-xs">{@html text}</div>
    
              
        </div>
      </div>
      
    </div>
  </div>
</div>

<style global lang="postcss">
  .tw {
    @tailwind utilities;
    @tailwind components;
    @tailwind base;

    .teaserdata_preview {
      display: flex;

     
    }
  
  }
</style>

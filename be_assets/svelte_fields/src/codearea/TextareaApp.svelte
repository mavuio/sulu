<script>
  import { onMount, tick } from "svelte";

  export let props;

  export let helpers;


  let code="n/a";

  function setCode() {
    props.onChange(code);
  }


  onMount((args) => {
    code=props.value;
    // console.log("#log 6113 SVELTE LOADED CODEAREA ➜ propsBEFORE", props);
    // console.log("#log 6113 SVELTE LOADED CODEAREA ➜ helpers", helpers);
  });
</script>

<div class="tw">
  <div class="codearea-wrapper">
    <div class="codearea">
        
        <textarea bind:value={code} on:input={setCode} 
        
        class="w-full h-[50vh] text-xs font-mono border-gray-300 rounded-sm"
        />

    </div>
  </div>
</div>

<style global lang="postcss">
  .tw {
    @tailwind utilities;
    @tailwind components;
    @tailwind base;

    .codearea {
  }
}
</style>

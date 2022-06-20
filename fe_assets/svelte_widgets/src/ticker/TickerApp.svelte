<script>
  import Ticker from "./Ticker.svelte";
  export let text = "   ";
  let props = {
    direction: "left",
    duration: Math.ceil(text.length * 0.5),
    delay: 0,
    loop: true,
    pausing: true,
    alternate: false,
    behavior: "always", // auto only animates if too wide
  };
</script>

<div class="text-lg uppercase text">
  <Ticker {...props}>
    <p class="pr-20">{text}</p>
  </Ticker>
</div>

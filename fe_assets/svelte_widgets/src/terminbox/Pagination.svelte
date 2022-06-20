<script>
  import ChevronLeftIcon from "./ChevronLeft.svelte";
  import ChevronRightIcon from "./ChevronRight.svelte";
  import { createEventDispatcher } from "svelte/internal";

  const dispatch = createEventDispatcher();

  export let current = 1;
  export let num_items = 120;
  export let per_page = 5;
  const s = 12;

  $: num_pages = Math.ceil(num_items / per_page);

  let arr_pages = [];

  function buildArr(c, n) {
    if (n <= 7) {
      return [...Array(n)].map((_, i) => i + 1);
    } else {
      if (c < 3 || c > n - 2) {
        return [1, 2, 3, "...", n - 2, n - 1, n];
      } else {
        return [1, "...", c - 1, c, c + 1, "...", n];
      }
    }
  }

  function setArrPages() {
    arr_pages = buildArr(current, num_pages);
  }

  $: if (current) {
    setArrPages();
  }

  $: if (per_page) {
    setArrPages();
    current = 1;
  }

  $: if (num_items) {
    num_pages = Math.ceil(num_items / per_page);
    current = current || 1;
  }

  function setCurrent(i) {
    if (isNaN(i)) return;
    current = i;
    dispatch("navigate", current);
  }
</script>

<div class="flex text-base ">
  <div
    class="h-12 w-12 mr-1 flex justify-center items-center
      {current > 1 ? 'cursor-pointer' : 'opacity-40'}"
    on:click={() => current > 1 && setCurrent(current - 1)}
  >
    <ChevronLeftIcon class="w-8 h-8" />
  </div>
  <div class="flex h-12 font-medium ">
    {#each arr_pages as i}
      <div
        class="min-w-[2rem] sm:flex justify-center items-center hidden select-none
          cursor-pointer leading-5 transition duration-150 ease-in {i == current
          ? 'border-b-2 border-white'
          : 'border-b-2 border-transparent'}
          "
        on:click={() => setCurrent(i)}
      >
        {i}
      </div>
    {/each}
    <div
      class="flex items-center justify-center w-12 h-12 leading-5 transition duration-150 ease-in border-b-2 border-white cursor-pointer select-none sm:hidden"
    >
      {current}
    </div>
  </div>
  <div
    class="h-12 w-12 ml-1 flex justify-center items-center 
      {current < num_pages ? 'cursor-pointer' : 'text-gray-400'}"
    on:click={() => current < num_pages && setCurrent(current + 1)}
  >
    <ChevronRightIcon class="w-8 h-8" />
  </div>
</div>

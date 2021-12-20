<template>
  <div class="sd-author-types-table-wrapper">
    <div class="sd-author-types-table">
      <table class="sd-author-types-table--table">
        <tr>
          <th>Role</th>
          <th>View</th>
          <th>Edit</th>
          <th>On Author List</th>
        </tr>
        <tr>
          <td>Author</td>
          <td>&check;</td>
          <td>&check;</td>
          <td>&check;</td>
        </tr>
        <tr>
          <td>Corresponding Author</td>
          <td>&check;</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Curator</td>
          <td>&check;</td>
          <td>&check;</td>
          <td></td>
        </tr>
      </table>

    </div>
  </div>
</template>

<script>

export default {

    name: 'AuthorTypesTable',
}
</script>

<style lang="scss">
  .sd-author-types-table-wrapper {
    font-size: 0.687rem;
  }

  .sd-author-types-table {
    position: relative;
    display:flex;
    justify-content: flex-end;
    padding-right: 1.5rem;

    border-collapse:collapse;

    th, td {
      padding: 1px 4px;
      border: solid 1px #fff;
      min-width: 6rem;
      text-align: center;
    }
    th:nth-child(1), td:nth-child(1) {
      text-align:left;
    }
  }
</style>
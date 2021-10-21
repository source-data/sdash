<template>
  <b-container class="sd-404-page pt-3">
    <b-row>
      <b-col cols md="12">
        <h1>Page Not Found</h1>
        <p>There is no page at this URL.</p>
        <p><router-link :to="{name:'dashboard'}">Return to Dashboard</router-link></p>
      </b-col>
    </b-row>
  </b-container>
</template>
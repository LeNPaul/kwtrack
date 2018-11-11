$(function() {

  $("#search-btn").on("click", function() {
    console.log("Asdfasdfasdf");
    var kw = $("#keyword").text();
    $.ajax({
      type: "POST",
      url: "https://sellercentral.amazon.com/sspa/hsa/cm/keywords/power",
      contentType: "application/json",
      data: {"pageId":"https://www.amazon.com/HSA/pages/default","keywordList":[{"key":"violin bow","matchType":"EXACT"},{"key":"violin bow","matchType":"BROAD"}]},
      cookie: "gsScrollPos-2042=; gsScrollPos-2106=; gsScrollPos-4060=0; gsScrollPos-6635=0; gsScrollPos-9883=0; gsScrollPos-2608=0; gsScrollPos-554=0; gsScrollPos-1840=0; gsScrollPos-544=0; gsScrollPos-1578=0; gsScrollPos-1459=; gsScrollPos-1645=0; gsScrollPos-10635=0; gsScrollPos-456882153=0; gsScrollPos-456892155=0; gsScrollPos-456893473=0; gsScrollPos-456897459=; kdp-lc-main=en_US; s_ev22=%5B%5B%27magnets%27%2C%271500778149588%27%5D%5D; s_evar1=magnets; lwa-context=5f3f64842b684ea6fe5593d26b741450; gsScrollPos-3428=; gsScrollPos-1921=; gsScrollPos-7647=580; gsScrollPos-719=; gsScrollPos-1462=836; aws_lang=en; aws-target-static-id=1506975282040-297425; gsScrollPos-888=0; JSESSIONID=B4C09DA81ED9F6E54248D29FC0152A74; appstore-devportal-locale=en_US; AMCVS_4A8581745834114C0A495E2B%40AdobeOrg=1; sid=\"5RhFGI5h+CiK4JwTOpDMGQ==|a/iWQ7J3XDQHFT+WiA4gtRL9t2ANJwu3QJJK6jSR3dM=\"; __utmc=194891197; aws-target-data=%7B%22support%22%3A%221%22%7D; regStatus=pre-register; aws-ubid-main=685-6526155-4507080; gsScrollPos-2874=0; gsScrollPos-7844=0; devportal-state-share=eyJzdXBwb3J0ZWRMYW5ndWFnZXMiOnsiZW5fVVMiOiJFbmdsaXNoIiwiemhfQ04iOiLkuK3mlocgKENoaW5lc2UpIiwiamFfSlAiOiLml6XmnKzoqp4gKEphcGFuZXNlKSJ9LCIxMDk5IjowfQ; devportal-redirect=\"https://developer.amazon.com\"; p2dPopoverID_141-8790147-1133222=1; gsScrollPos-16735=0; gsScrollPos-17240=; gsScrollPos-17501=0; skin=noskin; _ga=GA1.2.1786894269.1510607771; ajs_user_id=null; ajs_group_id=null; _rails-root_session=UzdMa2lCQWhXZUF5b1hKbmI5c0JuWFJIVEFXcmhSM2V3cWhRZyt3SDZSSkNJUzZzY1pGcU1nTUE5V0ZSLzVVc2VXQjdHS2MrdytOM290MjNWOGlPd3kzaW5zOHdJamJ0cnhrU09wd3JTNDhwRzR3K1Njdmk5WG1va0FCTXR5ajFKcmozNnJUc0VjTXJCbE1nSmtJbk4xOWxOelk5Y05vSTlkMGNtUzVOZVhRUTJ6ek9qVVA4dU9GSGQyYkYzTW1WLS1vTFhwVGlaUU5UVFZhZkVNalgya21nPT0%3D--c617b1fc8de71fb98a17cffae63e5b28c03bffe3; gsScrollPos-389=; gsScrollPos-544=; amznacsleftnav-8b1cbb0f-3455-4731-bd98-3e8937ea257b=1; aws-target-visitor-id=1506975282042-215923.28_37; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-amazon.com-1527709577612-11102; ca=AFVBiAMgkAA0ChgEAgIIAQA=; pN=7; session-id-time-jp=2082787201l; ubid-acbjp=355-4624229-4688139; gsScrollPos-10698=0; referrer_session={%22full_referrer%22:%22https://forums.developer.amazon.com/questions/112810/request-an-access-token-1.html%22}; s_lv=1536015945042; AMCV_4A8581745834114C0A495E2B%40AdobeOrg=-330454231%7CMCIDTS%7C17778%7CMCMID%7C53569537610511886757940489326973850328%7CMCOPTOUT-1536023145s%7CNONE%7CMCAID%7CNONE%7CvVersion%7C3.1.2; aws-priv=eyJ2IjoxLCJldSI6MCwic3QiOjB9; s_vnum=1964077951033%26vn%3D6; x-wl-uid=1258/SgWe0+mryD9CXg/ObO+6kl/JOEZdI8oO2+s3SBvYSueTHSyCY7NUp3Oc1Mq/eDfVtOZdtB5cpMyHTq1rqsagpObJg++gt+MZVVUVRZT4lUSqTcb7xLyvpyKDJfoJqyhGkKCYZeQ=; session-id-eu=259-4190095-7478901; ubid-acbuk=258-6034852-3282430; x-acbuk=xKML5E2QMRHSmoNoio7MAmyyaq65qm8xYcNSwWgkYiHdYAK8gZmR6NkVNTtB1WhA; _mkto_trk=id:112-TZM-766&token:_mch-amazon.com-1527709577612-11102; s_vn=1573105947627%26vn%3D2; s_sq=%5B%5BB%5D%5D; s_dslv=1541576389018; s_nr=1541576389021-Repeat; __utma=194891197.1786894269.1510607771.1541576153.1541578303.4; __utmz=194891197.1541578303.4.3.utmccn=(referral)|utmcsr=google.ca|utmcct=/|utmcmd=referral; s_pers=%20s_vnum%3D1928715348834%2526vn%253D29%7C1928715348834%3B%20s_fid%3D1F2657E4385752EF-3973655053ECEE8F%7C1699399638354%3B%20s_dl%3D1%7C1541720130995%3B%20gpv_page%3DUS%253ASC%253A%2520SellerCentralLogin%7C1541720131002%3B%20s_ev15%3D%255B%255B%2527ELUSabi-l.facebook.com%2527%252C%25271541633238362%2527%255D%252C%255B%2527Typed/Bookmarked%2527%252C%25271541718331009%2527%255D%255D%7C1699484731009%3B; s_sess=%20ev1%3Dn/a%3B%20s_ppvl%3DUS%25253AAS%25253AABI-overview%252C36%252C36%252C1068%252C1920%252C1068%252C1920%252C1200%252C1%252CL%3B%20s_ppv%3DUS%25253AAS%25253AABI-overview%252C100%252C36%252C3002%252C1920%252C1068%252C1920%252C1200%252C1%252CL%3B%20s_cc%3Dtrue%3B%20c_m%3DundefinedTyped/BookmarkedTyped/Bookmarked%3B%20s_sq%3Dwebstoreamznhelpandeducation%253D%252526pid%25253Dhttps%2525253A//sellercentral.amazon.com/cu/case-dashboard/view-case%2525253Fref%2525253Dsc_cd_lobby_vc_v3%25252526ie%2525253DUTF%25252526caseID%2525253D5503873551%25252526View%2525252Bor%2525252BRespond%2525253DSubmit%252526oid%25253Dhttps%2525253A//sellercentral.amazon.com/gp/homepage.html/ref%2525253Dxx_home_logo_xx%252526ot%25253DA%3B; at-main=Atza|IwEBIJjo_enh21ilTGwKJJ03fSPF_BomowOoljSYux1mW0vWH43aPyxgabxF7_Xp-qLiugRNIER-SoVO6yq4nS_fSUx8BnGAj_gsm54ikJvuJM1uqSarRTZBaPCsjTm7_XmR7erPj0cM-eu2dfvcKyPev7ez8FFTTrNboVR8iX6vPsI4LXYtV61NvCaLSO6HfbhR4tz5YNQw0YXx3s8sT4w_Yene53ySSRiDWX503zkh3j8s-oeUNDKM3n1ZoORLpPQ3NW-toGtTOF_CKVldPvyLu5NLXZl2X0DtBX6iqFSVE2Hgu266GYfOZr1Bf31lb9yaPjj7kbxL-oaip902P-oRhjky0EFiyKMs5XcyBDdHPLKnzmCswDY60jMmFv4o91buU6njhkf9TNaUzdtvxBeqpUO8WAQdueZxi8JxES2SUYBBKw; sess-at-main=\"jDjIDTCtus6BGwD8l9Lk7bSCNiwN8rRACCmjOLrX7kg=\"; sst-main=Sst1|PQEeKkBM8r3cOOIQ9jT-4IDjFByQKOve7LuSx6w3dCoYQ8SEjCErRaecnDm8D4pFMTR4A0n9Gaz1RhuU5_Ej_Y8X4egRMdKIBVLau0gQ4Qv2rO4790jOtnuAfC-jw1ViQMpxuJmPKO2Gji_di6Dhwrfe7DH11QIiwxjOVOBI5c0AnOkPRtoGsv0sjXc1TP_OScyGj27avYe8xSemNehOkbnUaP1xAEzL5VJTmnAshhUsxAKMrKp7o58J0TA3gJkNbnaOXdu8xCEwh5h07lfgD2cfIdZ6ar5Rv38bAhp6WwYy_2lmEqoDVR01owaoA_A3jjYpBvcgOzgEmu1QUu6L9pd2is-ci8fl3GKRn936VbbeDVDkp_lf_ff4cHrvhK8HyK2VKhuUD4S9a0y0zejIm4fg4n4QKEAesFm63Jhcj2-oPSF3ShBd1yXndblOHBJSy0LwPR_WLkZqMQ_QWXbXO1May67KDLiDQBb_y33YJOIKazhRMk7hokA9gU2kzjjnwSXNxQb6U3zEAI3x1iDb5YUIJA; session-id=136-7081483-3776358; ubid-main=134-5269906-2311309; session-token=\"kKNY9vVlB5jjryzxiQY0rh393FrtxTZ6TcFWCAI3P/mC7NnQ3cXOj32CB4xd7EN6Zdh0LmcUCydDFXi2IbGVwD5LTZsGfNMo+gUegt3MHtfMx9o2EEjkNZGiS4u/+MocmiFGvuYTJ9mppR2LAespFHrx1SRlx37+U5+JfT8rl3LpjAE+TpUbPY7pbTqhHdw75SyJtBfhwEvTbh3ePoK+zyFSkgnoVJ254dQosI93rEs=\"; x-main=5kQY6292G8pZDZD8aUyXBtxXh8r9CCpVuqXIapqumRVIXl67j7LUkf6mpz5B6oC1; session-id-time=2082787201l; csm-hit=tb:Z0C7SDZ5DBMGFYGEF8T8+s-Z0C7SDZ5DBMGFYGEF8T8|1541721054855&t:1541721054855&adb:adblk_no",
      success: function(r) {
        console.log(r);

        "object" === ("undefined" == typeof r ? "undefined" : _typeof(r)) && 0 !== r.length || alert("Please sign into your SellerCentral to get the Search Volume data.");
        var a = !0,
            i = !1,
            s = void 0;
        try {
          for (var c, u = e[Symbol.iterator](); !(a = (c = u.next()).done); a = !0) {
            var d = c.value,
                p = null,
                f = null;
            try {
              p = r.find(function(e) {
                return e.keyword.toLowerCase() === d[1].toLowerCase() && "EXACT" === e.matchType
              }), f = r.find(function(e) {
                return e.keyword.toLowerCase() === d[1].toLowerCase() && "BROAD" === e.matchType
              })
            } catch (h) {}
            d[d.length - 2] = p ? Math.round(30 * p.impression) : "NONE", d[d.length - 1] = f ? Math.round(30 * f.impression) : "NONE"
          }
        } catch (b) {
          i = !0, s = b
        } finally {
          try {
            !a && u["return"] && u["return"]()
          } finally {
            if (i) throw s
          }
        }
        $("#search-btn").prop("disabled", !1), $("#search-btn").html("Check Now"), $("#kw-btn").prop("disabled", !1), $("#kw-btn").html("Relevant KW"), "1" !== localStorage.getItem("amz_isSubscriber") && $(".amz_notSubscribe_table_data").show(), n.width("100%"), t.html("Finished!"), l(e, o)
      },
      error: function(d) {
        console.log(d);
      }
    });
  });

});


<template>
    <view class="grace-padding">
        <view class="myface"><image :src="myFace" mode="widthFix"></image></view>
		<view style="text-align: center;margin: 10px 0;">
			{{user.u_name}} <text style="color:#888888;" @tap="logoff">注销</text>
		</view>
        <view class="grace-box-banner" style="margin:30rpx 0;">
            <view class="garce-items">
                <view class="line1">1</text></view>
                <view class="line2">投递</view>
            </view>
            <view class="garce-items">
                <view class="line1">10</text></view>
                <view class="line2">积分</view>
            </view>
            <view class="garce-items">
                <view class="line1">700</text></view>
                <view class="line2">芝麻信用</view>
            </view>
            <view class="garce-items">
                <view class="line1">0</text></view>
                <view class="line2">消息</view>
            </view>
        </view>
        <view class="grace-title grace-nowrap grace-space-between">
            <view class="grace-h5 grace-blod">购物车</view>
        </view>
        <view class="myart-list" v-for="(item, index) in arts" :key="index">
            <view class="title">{{item.art_title}}</view>
            <view class="btns">
                <view :data-artid="item.art_id"  @tap="editArt">编辑</view>
                <view :data-artid="item.art_id" :data-index="index" @tap="removeArt">删除</view>
            </view>
        </view>
        <view class="loadMore" @tap="getArtList">{{loadMore}}</view>
    </view>
	
</template>
<script>
var _self, loginRes, page = 1;
export default {
    data() {
        return {
            myFace : '',
			user:{},
            arts : [],
            loadMore : "点击加载更多"
        };
    },
    onLoad:function(){
        _self = this;
        loginRes = this.checkLogin('../my/my', '2');
        if(!loginRes){return false;}
        this.myFace = loginRes[3];
    },
    onShow:function(){
		loginRes = this.checkLogin('../my/my','2');
		if(!loginRes){return false;}
		//加载会员信息
		uni.request({
			url:this.apiServer + 'my&m=info',
			method:'POST',
			header:{'content-type' : "application/x-www-from-urlencoded"},
			data:{
				uid    :loginRes[0],
				random :loginRes[1] 
			},
			success: (res) => {
				console.log(res);
				if(res.data.status == 'OK'){
					this.user = res.data.data;
				}
			}
		});
        //加载我的文章
        this.arts = [];
        page = 1;
        this.loadMore = '点击加载更多';
		this.getArtList();
		
    },
    methods:{
		logoff : function(){
			uni.removeStorageSync('SUID');
			uni.removeStorageSync('u_random');
			uni.showToast({
				title:"您已经退出家教超市",
				icon:"none"
			});
			setTimeout(function(){
				uni.switchTab({
					url:'../index/index'
				});
			},1000);
		},
		editArt : function(e){
			var artId = e.currentTarget.dataset.artid;
			uni.navigateTo({
				url:"../editArt/editArt?artId="+artId
			});
		},
        removeArt : function(e){
            var artId = e.currentTarget.dataset.artid;
            var index = e.currentTarget.dataset.index;
            uni.showModal({
                title:"提示",
                content:"确定要删除吗?",
                success:function(e){
                    if(e.confirm == true){
                        uni.request({
                            
                            url: _self.apiServer + 'my&m=removeArt',
                            method: 'POST',
                            header: {'content-type' : "application/x-www-form-urlencoded"},
                            data: {
                                uid : loginRes[0],
                                random : loginRes[1],
                                artId : artId
                            },
                            success:function(res){
                                
                                if(res.data.status == 'ok'){
                                    uni.showToast({title: "已删除", icon:"none"});
                                    _self.arts.splice(index, 1);
                                }else{
                                    uni.showToast({title: "删除失败", icon:"none"});
                                }
                            }
                        });
                    }
                }
            });
        },
        getArtList : function(){
            if(this.loadMore != '点击加载更多'){return ;}
            this.loadMore = '加载中...';
            uni.request({
                url: this.apiServer + 'my&m=arts&page='+page,
                method: 'POST',
                header: {'content-type' : "application/x-www-form-urlencoded"},
                data: {
                    uid : loginRes[0],
                    random : loginRes[1]
                },
                success: res => {
                    console.log(res);
                    if(res.data.status == 'ok'){
                        this.arts = this.arts.concat(res.data.data);
                        page++;
                        this.loadMore = '点击加载更多';
                    }else if(res.data.status == 'empty'){
                        this.loadMore = '已经加载全部';
                    }else{
                        this.loadMore = '点击加载更多';
                    }
                }
            });
        }
    }
}
</script>
<style>
.myface{width:88px; height:88px; border:5px solid #F1F2F3; border-radius:100%; margin:15px auto;}
.myface image{width:100%; border-radius:100%;}
.myart-list{width:100%; margin:8px 0; overflow:hidden; border-bottom:1px dashed #D7D8D9;}
.myart-list .title{line-height:2em; width:100%;}
.myart-list .btns{line-height:2em; width:100%;}
.myart-list .btns view{float:right; padding:0 6px; margin:0 5px; color:#00B26A;}
.myart-list .btns view:last-child{color:#F76260;}
.loadMore{width:100%; padding:8px 0; text-align:center; color:#CCCCCC;}
</style>
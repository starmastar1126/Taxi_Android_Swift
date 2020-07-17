//
//  OpenListView.swift
//  DriverApp
//
//  Created by Admin on 15/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class OpenListView: NSObject , UITableViewDataSource , UITableViewDelegate{
    
    typealias CompletionHandler = (_ selectedItemId:Int) -> Void
    
    var currentInst:OpenListView!
    var uv:UIViewController!
    var containerView:UIView!
    var handler:CompletionHandler!
    var listView:ListView!
    var bgView:UIView!
    let generalFunc = GeneralFunctions()
    let closeImgTapGue = UITapGestureRecognizer()
    var arrListObj = [String]()
    var listHeightContainer = [CGFloat]()
    
    var navZposition:CGFloat = 0
    
    init(uv:UIViewController, containerView:UIView){
        super.init()
        
        self.uv = uv
        self.containerView = containerView
        
    }
    
    
    func show(listObjects : [String] , title : String,currentInst:OpenListView, handler: @escaping CompletionHandler){
        
        self.listHeightContainer = []
        
        self.handler = handler
        self.currentInst = currentInst
        
        bgView = UIView()
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.5
        //        bgView.frame = self.containerView.frame
        bgView.frame = CGRect(x:0, y:0, width: Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgView.isUserInteractionEnabled = true
        
        let bgViewTapGesture : UITapGestureRecognizer = UITapGestureRecognizer()
        bgViewTapGesture.addTarget(self, action: #selector(currentInst.removeView))
        bgView.addGestureRecognizer(bgViewTapGesture)
        //Utils.createRoundedView(view: listView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        arrListObj = listObjects
        
        let width = (Application.screenSize.width > 390 ? 375 : (Application.screenSize.width - 40))
        
        let paddingTopBottom : CGFloat = 44
        let heightTitleLbl : CGFloat = 50
        var totalCellsHeight : CGFloat = 0
        
        for i in 0 ..< arrListObj.count{
            let listNameHeight = arrListObj[i].height(withConstrainedWidth: width - 100, font: UIFont(name: "Roboto-Regular", size: 17)!)
            self.listHeightContainer += [listNameHeight + 26]
            totalCellsHeight += (listNameHeight + 26)
        }
        
        var extraHeight : CGFloat =  totalCellsHeight + paddingTopBottom + heightTitleLbl
        if extraHeight > Application.screenSize.height
        {
            extraHeight = Application.screenSize.height - 70
        }
        
        listView = ListView(frame: CGRect(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2, width: width, height: CGFloat(extraHeight)))
        listView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        listView.tableView.dataSource = currentInst
        listView.tableView.delegate = currentInst
        listView.titleLabelTxt.text = title
        listView.tableView.tableFooterView = UIView()
        listView.tableView.allowsSelection = true
        listView.tableView.register(ListTVCell.self, forCellReuseIdentifier: "ListTVCell")
        listView.tableView.register(UINib(nibName: "ListTVCell", bundle: nil), forCellReuseIdentifier: "ListTVCell")
        
        closeImgTapGue.addTarget(self, action: #selector(currentInst.removeView))
        listView.closeImgView.isUserInteractionEnabled = true
        listView.closeImgView.addGestureRecognizer(closeImgTapGue)

        
        listView.layer.shadowOpacity = 0.5
        listView.layer.shadowOffset = CGSize(width: 0, height: 3)
        listView.layer.shadowColor = UIColor.black.cgColor
        
        listView.titleLabelTxt.fitText()
        listView.tableView.reloadData()
        
//        let currentWindow = Application.window
//
//        if(currentWindow != nil){
//            currentWindow?.addSubview(bgView)
//            currentWindow?.addSubview(listView)
//        }else{
//            self.uv.view.addSubview(bgView)
//            self.uv.view.addSubview(listView)
//        }
        let currentWindow = Application.window
        
        if(self.uv == nil){
            currentWindow?.addSubview(bgView)
            currentWindow?.addSubview(listView)
        }else if(self.uv.navigationController != nil){
            self.uv.navigationController?.view.addSubview(bgView)
            self.uv.navigationController?.view.addSubview(listView)
            
            listView.tag = Utils.ALERT_DIALOG_CONTENT_TAG
            bgView.tag = Utils.ALERT_DIALOG_BG_TAG
            navZposition = self.uv.navigationController!.navigationBar.layer.zPosition
            self.uv.navigationController?.navigationBar.layer.zPosition = -1
        }else{
            self.uv.view.addSubview(bgView)
            self.uv.view.addSubview(listView)
        }
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrListObj.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ListTVCell", for: indexPath) as! ListTVCell
        
        cell.listLabelTxt.text = arrListObj[indexPath.row]
        cell.listLabelTxt.fitText()
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        self.removeView()
        if(self.handler != nil){
            self.handler(indexPath.row)
        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return self.listHeightContainer[indexPath.row]
    }
    
    func removeView(){
        listView.frame.origin.y = Application.screenSize.height + 2500
        listView.removeFromSuperview()
        bgView.removeFromSuperview()
        if(self.uv != nil){
            self.uv.navigationController?.navigationBar.layer.zPosition = navZposition
        }
//        self.uv.view.layoutIfNeeded()
    }
}


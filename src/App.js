import React, { Component } from "react";
import "./App.css";
import QrReader from "react-qr-reader";
import {
  FlexBox,
  FlexBoxDirection,
  FlexBoxJustifyContent,
  Table,
  TableCell,
  Label,
  TableRow,
  TableColumn,
  ShellBar,
} from "@ui5/webcomponents-react";
import moment from "moment";

//const authToken = '4ddb8b6d5ca7ddbf-86d1772a758fef0d-4a1f4580b44006b5';

//const { ViberClient } = require('messaging-api-viber');

const MOMENT = require("moment");

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      data: "NoResult",
      delay: 200,
      result: "Ready",
      qrList: [
        { id: 0, qr_code: "QR_Code_Test", date: "12052012", time: "12:35" },
      ],
      success_add_sql: true,
    };

    this.handleScan = this.handleScan.bind(this);
  }

  handleScan(result) {
    console.log(this.state.success_add_sql);
    if (result && this.state.success_add_sql) {
      this.setState({ success_add_sql: false });
      let qrList = this.state.qrList;
      let qrOne = {};
      console.log(result);
      qrOne.qr_code = result;
      const date = MOMENT(new Date()).format("YYYY-MM-DD HH:mm:ss");
      console.log(date);
      const time = new Date().toLocaleTimeString();
      qrOne.date = date;
      qrOne.time = time;
      this.setState({ result });
      console.log(qrOne);
      console.log(qrList);
      let qrOld = qrList[qrList.findIndex((x) => x.qr_code === qrOne.qr_code)];
      console.log(qrOld);
      let diff;
      if (qrOld) {
        diff = moment(qrOne.date).diff(moment(qrOld.date), "seconds");
        console.log(diff);
      }
      if (diff > 30 || !qrOld) {


        /*fetch("https://denscan.belsap.com/set_webhook.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(qrOne),
        })
          .then((res) => {
            console.log(res);
            return res.json();
          })
          .then((data) => {
            console.log(data)}); */


        fetch("https://denscan.belsap.com/php/crud/insert_qr_sql.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(qrOne),
        })
          .then((res) => {
            console.log(res);
            return res.json();
          })
          .then((data) => {
            console.log(data);
            if (data.success) {
              qrOne.id = data.id;
              qrList.unshift(qrOne);
              qrList.sort((a, b) => {
                let result;
                if (a.id > b.id) result = -1;
                if (a.id < b.id) result = 1;
                return result;
              });
              this.setState({ qrList,success_add_sql: true });
              console.log(this.state.success_add_sql);


             /* const client = new ViberClient({
                accessToken: authToken,
                 sender: {
                  name: 'DenScan',
                },
              });


              client.setWebhook('https://denscan.belsap.com').catch((error) => {
                console.log(error); // formatted error message
                console.log(error.stack); // error stack trace
                console.log(error.config); // axios request config
                console.log(error.request); // HTTP request
                console.log(error.response); // HTTP response
              });

           




                client.sendText('hEh2QOCKK/e5n4/ajSYBvQ==', 'Hello', {
                  keyboard: {
                    defaultHeight: true,
                  },
                });*/

            }
          })
          .catch((err) => {
            console.log(err);
          });
      } else {
        this.setState({ success_add_sql: true });
      }
    }
  }

  handleError(err) {
    console.error(err);
  }

  render() {
    const previewStyle = {
      height: "100%",
      width: "100%",
    };

    return (
      <div>
        <ShellBar
          logo={
            <img
              alt = ""
              src = "http://localhost:3000/pic_time.jpg"
            />
          }
          secondaryTitle="School time counter"
        ></ShellBar>
        <FlexBox
          style={{ width: "95%", border: "1px" }}
          justifyContent={FlexBoxJustifyContent.SpaceBetween}
          direction={FlexBoxDirection.Row}
        >
          <FlexBox
            style={{ width: "95%", border: "1px" }}
            justifyContent={FlexBoxJustifyContent.SpaceBetween}
            direction={FlexBoxDirection.Column}
          >
            <p>{this.state.result}</p>
            <QrReader
              delay={this.state.delay}
              style={previewStyle}
              onError={this.handleError}
              onScan={this.handleScan}
            />
          </FlexBox>
          <Table
            columns={
              <>
                <TableColumn minWidth={12} popinText="Data">
                  <Label>ID</Label>
                </TableColumn>
                <TableColumn minWidth={12} popinText="Data">
                  <Label>QR Code</Label>
                </TableColumn>
                <TableColumn minWidth={12} popinText="Data">
                  <Label>Data</Label>
                </TableColumn>
                <TableColumn demandPopin minWidth={12} popinText="Time">
                  <Label>Time</Label>
                </TableColumn>
              </>
            }
          >
            {this.state.qrList.map((element) => (
              <TableRow>
                <TableCell>
                  <Label>{element.id}</Label>
                </TableCell>
                <TableCell>
                  <Label>{element.qr_code}</Label>
                </TableCell>
                <TableCell>
                  <Label>{element.date}</Label>
                </TableCell>
                <TableCell>
                  <Label>{element.time}</Label>
                </TableCell>
              </TableRow>
            ))}
          </Table>
        </FlexBox>
      </div>
    );
  }
}

export default App;

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

const MOMENT= require( 'moment' );

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      data: "NoResult",
      delay: 200,
      result: "No result",
      qrList: [
        { id:0, qr_code: "QR_Code_Test", date: "12052012", time: "12:35" },
      ],
    };

    this.handleScan = this.handleScan.bind(this);
  }

  handleScan(result) {
    if (result) {
      let qrList = this.state.qrList;
      let qrOne = {};
      console.log(result);
      qrOne["qr_code"] = result;
      const date = MOMENT(new Date()).format('YYYY-MM-DD HH:mm:ss');
      console.log(date);
      const time = new Date().toLocaleTimeString();
      qrOne["date"] = date;
      qrOne["time"] = time;
      this.setState({ result });
      console.log(this.state.resultArray);
      console.log(this.state.result);
      console.log(qrOne);
      fetch("https://denscan.belsap.com/insert_qr_sql.php", {
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
            qrList.push(qrOne);
            qrList.sort((a, b) => {
              let result;
              if (a.id > b.id) result = -1;
              if (a.id < b.id) result = 1;
              return result;
            });
            this.setState({qrList});
          }
        })
        .catch((err) => {
          console.log(err);
        });    
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
              alt="DenScan"
              src="https://raw.githubusercontent.com/SAP/ui5-webcomponents-react/master/assets/Logo.png"
            />
          }
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
